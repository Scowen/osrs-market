<?php

    namespace application\models\db;

    use \Yii;
    use \CException as Exception;
    use \application\components\ActiveRecord as CActiveRecord;

/**
 * This is the model class for table "item_history".
 *
 * The followings are the available columns in table 'item_history':
 * @property integer $id
 * @property integer $item
 * @property integer $offers
 * @property integer $quantity
 * @property double $average
 * @property double $high
 * @property double $low
 * @property integer $updated
 * @property integer $created
 *
 * The followings are the available model relations:
 * @property Items $item0
 */
class ItemHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'item_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('item', 'required'),
			array('item, offers, quantity, updated, created', 'numerical', 'integerOnly'=>true),
			array('average, high, low', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, item, offers, quantity, average, high, low, updated, created', 'safe', 'on'=>'search'),
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
			'Item' => array(self::BELONGS_TO, '\\application\\models\\db\\Items', 'item'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'item' => 'Item',
			'offers' => 'Offers',
			'quantity' => 'Quantity',
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
		$criteria->compare('item',$this->item);
		$criteria->compare('offers',$this->offers);
		$criteria->compare('quantity',$this->quantity);
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
	 * @return ItemHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
