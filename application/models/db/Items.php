<?php

    namespace application\models\db;

    use \Yii;
    use \CException as Exception;
    use \application\components\ActiveRecord as CActiveRecord;

/**
 * This is the model class for table "items".
 *
 * The followings are the available columns in table 'items':
 * @property integer $id
 * @property string $name
 * @property integer $zybez_id
 * @property string $zybez_search
 * @property string $image
 * @property double $average
 * @property double $high
 * @property double $low
 * @property integer $updated
 * @property integer $created
 *
 * The followings are the available model relations:
 * @property ItemHistory[] $itemHistories
 */
class Items extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('zybez_search', 'required'),
			array('zybez_id, updated, created', 'numerical', 'integerOnly'=>true),
			array('average, high, low', 'numerical'),
			array('name', 'length', 'max'=>128),
			array('zybez_search', 'length', 'max'=>256),
			array('image', 'length', 'max'=>512),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, zybez_id, zybez_search, image, average, high, low, updated, created', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'History' => array(self::HAS_MANY, '\\application\\models\\db\\ItemHistory', 'item'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'zybez_id' => 'Zybez',
			'zybez_search' => 'Zybez Search',
			'image' => 'Image',
			'average' => 'Average',
			'high' => 'High',
			'low' => 'Low',
			'updated' => 'Updated',
			'created' => 'Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('zybez_id',$this->zybez_id);
		$criteria->compare('zybez_search',$this->zybez_search,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('average',$this->average);
		$criteria->compare('high',$this->high);
		$criteria->compare('low',$this->low);
		$criteria->compare('updated',$this->updated);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Items the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
