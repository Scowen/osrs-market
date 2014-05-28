<?php

    namespace application\models\db\auth;

    use \Yii;
    use \CException as Exception;
    use \application\components\ActiveRecord;

    /**
     * ActiveRecord Model: Authorisation Hierarchy
     *
     * This is the model class for table "auth_hierarchy".
     *
     * The following are the available columns in table:
     * @property integer $parent
     * @property integer $child
     *
     * The followings are the available model relations:
     * @property AuthItems $child0
     * @property AuthItems $parent0
     */
    class Hierarchy extends ActiveRecord
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
            return '{{auth_hierarchy}}';
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
                array('parent, child', 'required'),
                array('parent, child', 'numerical', 'integerOnly' => true),
                array('parent, child', 'safe', 'on'=>'search'),
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
                'Child' => array(self::BELONGS_TO, '\\application\\models\\db\\auth\\Item', 'child'),
                'Parent' => array(self::BELONGS_TO, '\\application\\models\\db\\auth\\Item', 'parent'),
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
                'parent' => Yii::t('app', 'Parent ID'),
                'child' => Yii::t('app', 'Child ID'),
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
            $criteria->compare('parent', $this->parent);
            $criteria->compare('child', $this->child);
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
