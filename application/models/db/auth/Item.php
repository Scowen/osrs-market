<?php

    namespace application\models\db\auth;

    use \Yii;
    use \CException as Exception;
    use \application\components\ActiveRecord;

    /**
     * ActiveRecord Model: Authorisation Assignment
     *
     * This is the model class for table "auth_assignments".
     *
     * The following are the available columns in table:
     * @property integer $id
     * @property string $name
     * @property integer $type
     * @property string $description
     * @property string $rule
     * @property string $data
     * @property integer $default
     *
     * The followings are the available model relations:
     * @property application\models\db\users\User[] $Users
     * @property application\models\db\auth\Items[] $Children
     * @property application\models\db\auth\Items[] $Parents
     */
    class Item extends ActiveRecord
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
            return '{{auth_items}}';
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
            // manually.)
            return array(
                array('name, type', 'required'),
                array('type, default', 'numerical', 'integerOnly' => true),
                array('name', 'length', 'max' => 64),
                array('description, rule, data', 'safe'),
                array('id, name, type, description, rule, data, default', 'safe', 'on' => 'search'),
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
                'users'     => array(self::MANY_MANY, '\\application\\models\\db\\user\\User', 'auth_assignments(item, user)'),
                'Children'  => array(self::MANY_MANY, '\\application\\models\\db\\auth\\Item', '{{auth_hierarchy}}(parent, child)'),
                'Parents'   => array(self::MANY_MANY, '\\application\\models\\db\\auth\\Item', '{{auth_hierarchy}}(child, parent)'),
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
                'id'            => Yii::t('app', 'Item ID'),
                'name'          => Yii::t('app', 'Item Name'),
                'type'          => Yii::t('app', 'Authorisation Type'),
                'description'   => Yii::t('app', 'Item Description'),
                'rule'          => Yii::t('app', 'Business Rule'),
                'data'          => Yii::t('app', 'Item Data'),
                'default'       => Yii::t('app', 'Default'),
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
            $criteria->compare('id', $this->id);
            $criteria->compare('name', $this->name, true);
            $criteria->compare('type', $this->type);
            $criteria->compare('description',$this->description, true);
            $criteria->compare('rule', $this->rule, true);
            $criteria->compare('data', $this->data, true);
            $criteria->compare('default', $this->default);
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
