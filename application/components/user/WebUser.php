<?php

    namespace application\components\user;

    use \Yii;
    use \CException;
    use \CEvent as Event;
    use \application\models\db\user\User;
    use \application\components\user\UserInterface;

    /**
     * Web User
     *
     * @author      Zander Baldwin <mynameiszanders@gmail.com>
     * @license     MIT/X11 <http://j.mp/mit-license>
     * @copyright   Zander Baldwin <http://mynameis.zande.rs>
     */
    class WebUser extends \CWebUser
    {

        /**
         * @access protected
         * @var UserInterface $user
         */
        protected $user;

        /**
         * Initialisation Method
         *
         * @access public
         * @return void
         */
        public function init()
        {
            parent::init();
            // Raise an "onEndUser" event.
            $this->onStartUser(new Event($this));
            // Determine whether the user is logged in.
            if(!$this->getState('isGuest') && preg_match('/^[1-9]\\d*$/', $this->getState('id'))) {
                // User is logged in (not a guest, and a valid user ID). Load the database model for the currently
                // logged in user so we can use their information throughout the request.
                $this->user = User::model()->findByPk($this->getState('id'));
                // Perform some security checks.
                $this->securityChecks();
                // Raise an event in case we wish to insert functionality later on.
                $this->onAuthenticated(new Event($this));
            }
            else {
                // The user is a guest. Force the "isGuest" state to be true, just in case it has been set incorrectly.
                $this->setState('isGuest', true);
                // Raise an event in case we wish to insert functionality later on.
                $this->onGuest(new Event($this));
            }
        }


        /**
         * Get: User ID
         *
         * @access public
         * @return integer|null
         */
        public function getId()
        {
            return is_object($this->user) && isset($this->user->id)
                ? (int) $this->user->id
                : null;
        }


        /**
         * Security Checks
         *
         * @access public
         * @return void
         */
        protected function securityChecks()
        {
            // Check a couple of things for security, like if the user is on the same IP address and browser that
            // they used to log in with. Also check that the user exists in the database, and has not somehow been
            // banned from the system.
            if(
                $this->getState('userAgent') !== $_SERVER['HTTP_USER_AGENT']
             || !\application\components\IP::compare($this->getState('loginIp'), $_SERVER['REMOTE_ADDR'])
             || !is_object($this->user)
             || !$this->user->active
            ) {
                // If any of these simple checks fail, then log the user out immediately. Refer to the lengthy
                // explaination in the Logout controller as to why we pass bool(false).
                $this->logout(false);
                // Set a flash message explaining that the user has been logged out (nothing worse than being kicked
                // out without an explaination - people may complain about the system being faulty otherwise).
                $this->setFlash(
                    'logout',
                    Yii::t(
                        'application',
                        'You have been logged out because an attempted security breach has been detected. If this happens again please contact an administrator, as someone may be trying to access your account.'
                    )
                );
            }
        }


        /**
         * User Model
         *
         * @access public
         * @return User|mixed
         */
        public function model($property = null)
        {
            return $property !== null && is_object($this->user)
                ? $this->user->getAttribute($property)
                : $this->user;
        }


        /**
         * Get: Display Name
         *
         * @access public
         * @return string|null
         */
        public function getDisplayName()
        {
            return is_object($this->user)
                ? $this->user->displayName
                : null;
        }


        /**
         * Get: Full Name
         *
         * @access public
         * @return string|null
         */
        public function getFullName()
        {
            return is_object($this->user)
                ? $this->user->fullName
                : null;
        }


        /**
         * Check Access
         *
         * @access public
         * @param string|array $operation ""
         */
        public function checkAccess($operations, $params = array(), $allowCaching = true)
        {
            // If the list of operations is a string, split them up by commas.
            if(is_string($operations)) {
                $operations = preg_split('/\\s+,\\s+/', $operations, -1, PREG_SPLIT_NO_EMPTY);
            }
            // If the operations is an integer, turn it into an array ready to iterate.
            if(is_int($operations)) {
                $operations = (array) $operations;
            }
            // If the operations are not an array by now, then it's going to return false anyway. No point producing an
            // error by iterating over something that isn't an array.
            if(!is_array($operations)) {
                return false;
            }
            $return = true;
            foreach($operations as $operation) {
                $return = $return && parent::checkAccess($operation, $params, $allowCaching);
            }
            return $return;
        }

        /**
         * Get: Branch
         *
         * @access public
         * @return string
         */
        public function getBranch()
        {
            return $this->getState('branch', $this->model('branch'));
        }

        /* ================= *\
        |  EVENT DEFINITIONS  |
        \* ================= */


        /**
         * Event: Start User
         *
         * @access public
         * @return void
         */
        public function onStartUser(Event $event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }


        /**
         * Event: Guest
         *
         * @access public
         * @return void
         */
        public function onGuest(Event $event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }


        /**
         * Event: Authenticated
         *
         * @access public
         * @return void
         */
        public function onAuthenticated(Event $event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }

    }
