<?php

    namespace application\components\user;

    use \Yii;
    use \CException as Exception;
    use \CEvent as Event;
    USE \CDbCriteria as Criteria;
    use \application\models\db\user\User;
    use \application\models\db\user\FailedLogin;
    use \application\components\user\UserInterface;

    /**
     * User Identity
     *
     * Represents the data need to identify a user. It contains the authentication method used to check if the
     * credentials provided by the end-user are correct in order to authenticate that end-user with the details of the
     * associated user provided by the User model.
     */
    class Identity extends \CUserIdentity
    {

        /**
         * @var integer $id
         */
        protected $id;

        /**
         * Error Constants
         */
        const ERROR_IP_INVALID  = 3;
        const ERROR_THROTTLED   = 4;
        const ERROR_ID_INVALID  = 5;


        /**
         * Get: User ID
         *
         * This implementation is to override the default behaviour of getId() in CUserIdentity, which returns the
         * username as the user ID. We want the user ID to be the user ID, because y'know, that's what it is?
         *
         * @access public
         * @return integer
         */
        public function getId()
        {
            return $this->id;
        }


        /**
        * Load User
        *
        * Create an identity based upon the details a user in the database with the ID specified.
        *
        * @access public
        * @param integer|string $id
        * @return boolean
        */
        public function load($id)
        {
            // Attempt to load the user specified by the ID passed from the database.
            $user = is_int($id) || preg_match('/^[1-9]\\d*$/', $id)
                ? User::model()->findByPk($id)
                : User::model()->findByAttributes(array('username' => $id));
            // If a user with that ID does not exist, set the error code to ERROR_ID_INVALID, and return false.
            if(!is_object($user)) {
                $this->errorCode = self::ERROR_ID_INVALID;
                return false;
            }
            // Set the identity ID to the ID of the user we are loading.
            $this->id = (int) $user->id;
            // Set the user variables that we would like persisted accross subsequent HTTP requests in the session
            // state.
            $this->setPersistentStates(array(
                // The ID of the user that is logged in. This is required at the very least (if you don't specify a
                // user, who's logged in?)
                'id' => $this->id,
                // Set this to false to quickly identify that the user is not a guest and is, in fact, an authenticated
                // user. Yii doesn't seem to set this automatically, which is disappointing.
                'isGuest' => false,
            ));
            // Now that information has been store to the session state, specify that we did not come across an error
            // and return true.
            $this->errorCode = self::ERROR_NONE;
            return true;
        }


        /**
         * Authenticate User
         *
         * Without specifying all of the events, which are listed below, this method performs the following:
         *
         * - Load a model of the user defined by the username given.
         * - If the user does not exist in the database, set the error code to ERROR_USERNAME_INVALID, return false.
         * - Check that the user is allowed to login at the inbound IP address. If not, set the error code to
         *   ERROR_IP_INVALID, return false.
         * - Get the time of the last failed login for this user and make sure they are not within the throttling
         *   timeout. If they are, increment the number of premature attempts by one, set the error code to
         *   ERROR_THROTTLED, return false.
         * - Check that the password supplied matched the hash stored in the database. If it doesn't add a
         *   FailedLogin entry, set the error code to ERROR_PASSWORD_INVALID, return false.
         * - User has now passed authentication. Perform backwards compatibility here if need be.
         * - Set persistent states.
         * - Set error code to ERROR_NONE, return true.
         *
         * @event onAuthenticateStart
         * @event onUsernameInvalid
         * @event onUsernameValid
         * @event onLoginThrottled
         * @event onPasswordIncorrect
         * @event onPasswordCorrect
         * @event onStatesPersisted
         *
         * @access public
         * @return boolean
         */
        public function authenticate()
        {
            // Raise the "startAuthenticate" event.
            $this->onAuthenticateStart(new Event($this));
            // Load the model of the user defined by the username provided by the end-user.
            $criteria = new Criteria;
            $criteria->addColumnCondition(array(
                'username'  => $this->username,
                'email'     => $this->username,
            ), 'OR');
            $user = User::model()->find($criteria);
            // If the user does not exist in the database, or the user has been disabled (inactive), set the error code
            // to ERROR_USERNAME_INVALID, return false.
            if(!is_object($user) || !$user->active) {
                $this->errorCode = self::ERROR_USERNAME_INVALID;
                // Raise "onPasswordIncorrect" event; specifying that the password that the end-user entered was
                // incorrect.
                $this->onUsernameInvalid(new Event($this));
                return false;
            }
            // Store the user ID in a local scope variable so that we don't have to query the User model object each
            // time we reference it.
            $this->id = (int) $user->id;
            // Raise the "onUsernameValid" event; specifying that the username that the end-user entered has been found
            // in the database.
            $this->onUsernameValid(new Event($this));
            // Check that the user is allowed to login at the inbound IP address. If not, set the error code to
            // ERROR_IP_INVALID, return false.
            // You could also use "!in_array($_SERVER['REMOTE_ADDR'])" but this won't take into account wildcards.
            if(!$user->ipAllowed($_SERVER['REMOTE_ADDR'])) {
                $this->errorCode = self::ERROR_IP_INVALID;
                return false;
            }
            // Get the time of the last failed login for this user and make sure they are not within the throttling
            // timeout. If they are, increment the number of premature attempts by one, set the error code to
            // ERROR_THROTTLED, and return false.
            $lastAttempt = FailedLogin::model()->findByAttributes(
                array('user' => $this->id),
                array('order' => 'timestamp DESC')
            );
            if(!is_null($lastAttempt)) {
                // If we are going to throttle users that try login attempts too quickly, we need to determine what the
                // timeout is that defines how quickly is "too quickly". Set a default of half a second.
                $throttle = 0.5;
                if(isset(Yii::app()->settings['login.throttle'])) {
                    $throttle = Yii::app()->settings['login.throttle'];
                }
                elseif(isset(Yii::app()->params['login.throttle'])) {
                    $throttle = Yii::app()->params['login.throttle'];
                }
                // At what time does login throttling end?
                $throttling_ends = $lastAttempt->timestamp + $throttle;
                // Are we still within that throttling timeout?
                if(microtime(true) < $throttling_ends) {
                    // Increment the number of premature attempts made before the throttling timeout has ended, and save
                    // this result to the database.
                    $lastAttempt->attempts++;
                    $lastAttempt->save();
                    // Now that we have logged this attempt we can return the error.
                    $this->errorCode = self::ERROR_THROTTLED;
                    // Raise the "onLoginThrottled" event; specifying that the username that the end-user entered tried
                    // to log in too quickly following the previous attempt.
                    $this->onLoginThrottled(new Event($this));
                    return false;
                }
            }
            // Check that the password supplied matched the hash stored in the database. If it doesn't add a FailedLogin
            // entry, set the error code to ERROR_PASSWORD_INVALID, return false.
            if(!$user->verifyPassword($this->password)) {
                // A failed login attempt has occured, log it to the database for future security referencing.
                $failedLogin = new FailedLogin;
                $failedLogin->attributes = array(
                    'user'      => $this->id,
                    'timestamp' => microtime(true),
                    'password'  => sha1($this->password),
                    'ip'        => \application\components\IP::pton($_SERVER['REMOTE_ADDR']),
                );
                $failedLogin->save();
                // Now that we have logged this attempt we can return the error.
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
                // Raise the "onPasswordIncorrect" event; specifying that the password that the end-user entered was
                // incorrect.
                $this->onPasswordIncorrect(new Event($this));
                return false;
            }
            // Raise the "onPasswordCorrect" event; specifying that the password that the end-user entered was correct.
            $this->onPasswordCorrect(new Event($this));
            // Set the user variables that we would like persisted accross subsequent HTTP requests in the session
            // state.
            $this->setPersistentStates(array(
                // The ID of the user that is logged in. This is required at the very least (if you don't specify a
                // user, who's logged in?)
                'id'        => (int) $this->id,
                // Log the ID of the user that originally logged in. This should NEVER change, so that we can switch
                // between the authenticated user account and other user account should that user have the permission to
                // do so.
                'authId'    => (int) $this->id,
                // Keep track of the IP and User Agent that the user logged in with, if any of these change it it likely
                // that someone has stolen the session cookie and is trying to use it to masquerade themselves as the
                // original user.
                'loginIp'   => $_SERVER['REMOTE_ADDR'],
                'userAgent' => $_SERVER['HTTP_USER_AGENT'],
                // Set this to false to quickly identify that the user is not a guest and is, in fact, an authenticated
                // user. Yii doesn't seem to set this automatically, which is disappointing.
                'isGuest'   => false,
            ));
            // Raise the "onStatesPersisted" event; specifying that the variables to be saved in the user-specific
            // session have been defined.
            $this->onStatesPersisted(new Event($this));
            // Now that information has been store to the session state, specify that we did not come across an error
            // and return true.
            $this->errorCode = self::ERROR_NONE;
            // The credentials provided by the end-user have now been authenticated. Log the timestamp of this
            // successful login to the database.
            $user->lastLogin = microtime(true);
            $user->save();
            // Return the appropriate boolean value.
            return true;
        }


        /* ================= *\
        |  EVENT DEFINITIONS  |
        \* ================= */


        /**
         * Event: Start Authenticate Process
         *
         * @access public
         * @return void
         */
        public function onAuthenticateStart($event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }


        /**
         * Event: Username Invalid
         *
         * @access public
         * @return void
         */
        public function onUsernameInvalid($event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }


        /**
         * Event: Username Valid
         *
         * @access public
         * @return void
         */
        public function onUsernameValid($event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }


        /**
         * Event: Login Throttled
         *
         * @access public
         * @return void
         */
        public function onLoginThrottled($event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }


        /**
         * Event: Password Incorrect
         *
         * @access public
         * @return void
         */
        public function onPasswordIncorrect($event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }


        /**
         * Event: Password Correct
         *
         * @access public
         * @return void
         */
        public function onPasswordCorrect($event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }


        /**
         * Event: States Persisted
         *
         * @access public
         * @return void
         */
        public function onStatesPersisted($event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }

    }
