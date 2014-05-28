<?php

    namespace application\models\db\user;

    use \Yii;
    use \CException as Exception;
    use \application\components\ActiveRecord;
    use \application\components\user\UserInterface;

    /**
     * ActiveRecord Model: User
     *
     * This is the model class for table "users".
     *
     * The following are the available columns in table:
     * @property integer $id
     * @property string $username
     * @property string $email
     * @property string $password
     * @property integer $branch
     * @property string $firstname
     * @property string $nickname
     * @property string $lastname
     * @property integer $created
     * @property double $lastLogin
     * @property integer $active
     * @property string $avatar
     *
     * The followings are the available model relations:
     * @property AuthItems[] $AuthItems
     * @property FailedLogins[] $FailedLogins
     */
    class User extends ActiveRecord implements UserInterface
    {

        /**
         * @access protected
         * @var string $displayName
         */
        protected $displayName;

        /**
         * @access protected
         * @var string $fullName
         */
        protected $fullName;

        /**
         * Table Name
         *
         * Returns the name of the database table associated with this Active Record model.
         *
         * @access public
         * @return string "The associated database table name."
         */
        public function tableName()
        {
            return '{{users}}';
        }

        /**
         * Validation Rules
         *
         * Returns an array of validation rules that apply to the attributes of this model.
         *
         * @access public
         * @return array "Validation rules for model attributes."
         */
        public function rules()
        {
            // You should only define rules for attributes that will recieve user input. If an attribute does not have
            // any rules associated with it, then its value will not be changed by mass-assignment (will have to be done
            // manually).
            return array(
                array('email, password, firstname, lastname, created', 'required'),
                array('branch, created', 'numerical', 'integerOnly' => true),
                array('lastLogin', 'numerical'),
                array('username', 'length', 'max' => 64),
                array('email', 'length', 'max' => 255),
                array('password', 'length', 'max' => 60),
                array('firstname, nickname, lastname', 'length', 'max' => 128),
                array('avatar', 'length', 'max' => 256),
                array('active', 'boolean'),
                array('id, username, email, password, branch, firstname, nickname, lastname, created, lastLogin, active, avatar', 'safe', 'on' => 'search'),
            );
        }

        /**
         * Table Relations
         *
         * Returns an array of relational rules that determine how each attribute relates to another model, and the
         * extra class properties that become shortcuts to instances of those models.
         * You will most likely need to adjust these as the generated rules are guesswork at best.
         *
         * @access public
         * @return array "Relational rules."
         */
        public function relations()
        {
            // NOTE: you may need to adjust the relation name and the related
            // class name for the relations automatically generated below.
            return array(
                'AuthItems'     => array(self::MANY_MANY,   '\\application\\models\\db\\auth\\Item', '{{auth_assignments}}(user, item)'),
                'Events'        => array(self::HAS_MANY,    '\\application\\models\\db\\calendar\\Event', 'creator'),
                'FailedLogins'  => array(self::HAS_MANY,    '\\application\\models\\db\\user\\FailedLogin', 'user'),
                'Branch'        => array(self::BELONGS_TO,  '\\application\\models\\db\\Branch', 'branch'),
            );
        }

        /**
         * Customised Attribute Labels
         *
         * Returns an array of customised labels for each attribute.
         *
         * @access public
         * @return array "Customised attribute labels."
         */
        public function attributeLabels()
        {
            return array(
                'id'         => Yii::t('app', 'User ID'),
                'username'   => Yii::t('app', 'Username'),
                'email'      => Yii::t('app', 'Email Address'),
                'password'   => Yii::t('app', 'Password'),
                'branch'     => Yii::t('app', 'Branch'),
                'firstname'  => Yii::t('app', 'Firstname'),
                'nickname'   => Yii::t('app', 'Nickname'),
                'lastname'   => Yii::t('app', 'Lastname'),
                'created'    => Yii::t('app', 'Created'),
                'lastLogin'  => Yii::t('app', 'Last Login'),
                'active'     => Yii::t('app', 'Active?'),
                'avatar'     => Yii::t('app', 'Avatar URL'),
            );
        }

        /**
         * Search
         *
         * Retrieves a list of models based on the current search/filter conditions. A typical usecase involves:
         * - Initialize the model fields with values from filter form.
         * - Execute this method to get CActiveDataProvider instance which will filter models according to data in
         *   model fields.
         * - Pass data provider to CGridView, CListView or any similar widget.
         *
         * @access public
         * @return CActiveDataProvider "The data provider that returns models."
         */
        public function search()
        {
            $criteria = new \CDbCriteria;
            $criteria->compare('id',$this->id);
            $criteria->compare('username', $this->username, true);
            $criteria->compare('email', $this->email, true);
            $criteria->compare('password', $this->password, true);
            $criteria->compare('branch', $this->branch);
            $criteria->compare('firstname', $this->firstname, true);
            $criteria->compare('nickname', $this->nickname, true);
            $criteria->compare('lastname', $this->lastname, true);
            $criteria->compare('created', $this->created);
            $criteria->compare('lastLogin', $this->lastLogin);
            $criteria->compare('active', $this->active);
            $criteria->compare('avatar', $this->avatar, true);
            return new \CActiveDataProvider($this, array(
                'criteria' => $criteria,
            ));
        }

        /**
         * Model Instance
         *
         * Returns a static model of the specified ActiveRecord class. This exact method should be in all classes that
         * extend CActiveRecord.
         *
         * @access public
         * @param string $className "The active record class name."
         * @return self
         */
        public static function model($className = __CLASS__)
        {
            return parent::model($className);
        }


        /* ------------------------- *\
        |  END:   GII AUTOMATED CODE  |
        \* ------------------------- */

        /**
         * Get: Display Name
         *
         * @access public
         * @return string
         */
        public function getDisplayName()
        {
            if(!is_null($this->displayName)) {
                return $this->displayName;
            }
            $firstname = is_string($this->nickname) && $this->nickname
                ? ucwords($this->nickname)
                : ucwords($this->firstname);
            $this->displayName = $firstname . ' ' . ucwords(substr($this->lastname, 0, 1));
            return $this->displayName;
        }

        /**
         * Get: Full Name
         *
         * @access public
         * @return string
         */
        public function getFullName()
        {
            if(!is_null($this->fullName)) {
                return $this->fullName;
            }
            $this->fullName = ucwords($this->firstname) . ' ' . ucwords($this->lastname);
            return $this->fullName;
        }

        /**
         * Hash Password
         *
         * A useful function that can be called without creating a new instance of the User model, to transform a
         * string into a password hash.
         *
         * @static
         * @access public
         * @param string
         * @return string
         */
        public static function hashPassword($password)
        {
            $phpass = new \Phpass\Hash;
            return $phpass->hashPassword($password);
        }

        /**
         * Check Password
         *
         * Check that the password supplied to this method equates to the same password hash that is stored in the
         * database for the user identified by the current (this) model instance.
         *
         * @access public
         * @param string $password
         * @return boolean
         */
        public function verifyPassword($password)
        {
            return \CPasswordHelper::verifyPassword($password, $this->password);
        }

        /**
         * PHP Magic Function: Set
         *
         * Override the method to extend the functionality (hash a password that is set as an attribute before adding it
         * to the model).
         *
         * @access public
         * @param string $name
         * @param mixed $value
         * @return void
         */
        public function __set($property, $value)
        {
            // If an override method exists for a certain property, call it to alter the value before passing it to the
            // model to be saved to the database.
            $method = 'set' . ucwords($property);
            if(method_exists($this, $method)) {
                $value = $this->{$method}($value);
            }
            // Carry on setting it to the model as normal.
            parent::__set($property, $value);
        }

        /**
         * Set: Password
         *
         * @access protected
         * @param string $password
         * @return void
         */
        protected function setPassword($password)
        {
            return self::hashPassword($password);
        }

        /**
         * IP Allowed?
         *
         * Returns a boolean value to indicate whether the user can log in from the specified IP address.
         *
         * @access public
         * @param string $ip
         * @return boolean
         */
        public function ipAllowed($ip)
        {
            // Return true, whitelist and blacklists haven't been developed properly yet. This method, for now is just a
            // placeholder for the functionality provided to the UserIdentity component.
            return true;
        }

    }
