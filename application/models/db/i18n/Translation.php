<?php

    namespace application\models\db\i18n;

    use \Yii;
    use \CException as Exception;
    use \application\components\ActiveRecord;

    /**
     * ActiveRecord Model: Internationalisation Translations
     *
     * This is the model class for table "translations".
     *
     * The following are the available columns in table:
     * @property integer $id
     * @property string $language
     * @property string $translation
     *
     * The followings are the available model relations:
     * @property application\models\db\i18n\Message $Message
     */
    class Translation extends ActiveRecord
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
            return '{{translations}}';
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
                array('id, language, translation', 'required'),
                array('id', 'numerical', 'integerOnly' => true),
                array('language', 'length', 'max' => 16),
                array('id, language, translation', 'safe', 'on' => 'search'),
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
                'Message' => array(self::BELONGS_TO, '\\application\\models\\db\\i18n\\Message', 'id'),
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
                'id'            => Yii::t('app', 'Translation ID'),
                'language'      => Yii::t('app', 'Language'),
                'translation'   => Yii::t('app', 'Translation'),
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
            $criteria->compare('language', $this->language, true);
            $criteria->compare('translation', $this->translation, true);
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
