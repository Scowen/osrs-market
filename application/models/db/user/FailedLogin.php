<?php

    namespace application\models\db\user;

    use \Yii;
    use \CException as Exception;
    use \application\components\ActiveRecord;

    /**
     * ActiveRecord Model: User's Failed Login
     *
     * This is the model class for table "failed_logins".
     *
     * The following are the available columns in table:
     * @property integer $user
     * @property double $timestamp
     * @property string $password
     * @property string $ip
     * @property integer $attempts
     *
     * The followings are the available model relations:
     * @property application\models\db\user\User $User
     */
    class FailedLogin extends ActiveRecord
    {

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
            return '{{failed_logins}}';
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
                array('user, timestamp, password, ip', 'required'),
                array('user, attempts', 'numerical', 'integerOnly' => true),
                array('timestamp', 'numerical'),
                array('password', 'length', 'max' => 40),
                array('ip', 'length', 'max' => 16),
                array('user, timestamp, password, ip, attempts', 'safe', 'on' => 'search'),
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
            return array(
                'User' => array(self::BELONGS_TO, '\\application\\models\\db\\user\\User', 'user'),
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
                'user'      => Yii::t('app', 'User ID'),
                'timestamp' => Yii::t('app', 'Timestamp'),
                'password'  => Yii::t('app', 'Password'),
                'ip'        => Yii::t('app', 'IP Address'),
                'attempts'  => Yii::t('app', 'Number of Attempts'),
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
            // Please modify the following code to remove attributes that should not be searched.
            $criteria->compare('user', $this->user);
            $criteria->compare('timestamp', $this->timestamp);
            $criteria->compare('password', $this->password, true);
            $criteria->compare('ip', $this->ip, true);
            $criteria->compare('attempts', $this->attempts);
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

    }
