<?php

    namespace application\models\db;

    use \Yii;
    use \CException as Exception;
    use \application\components\ActiveRecord as CActiveRecord;

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property integer $active
 * @property integer $pro
 * @property integer $admin
 * @property integer $created
 */
class User extends CActiveRecord
{

	protected $fullName;
    protected $displayName;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password, email, created', 'required'),
			array('active, pro, admin, created', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>64),
			array('password', 'length', 'max'=>60),
			array('email', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, username, password, email, active, pro, admin, created', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'active' => 'Active',
			'pro' => 'Pro',
			'admin' => 'Admin',
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('active',$this->active);
		$criteria->compare('pro',$this->pro);
		$criteria->compare('admin',$this->admin);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
     * Display Name
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
        $this->displayName = $firstname;
        return $this->displayName;
    }


    /**
     * Full Name
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
     * Check Password
     *
     * Check that the password supplied to this method equates to the same password hash that is stored in the
     * database for the user identified by the current (this) model instance.
     *
     * @access public
     * @param string $password
     * @return boolean
     */
    public function password($password)
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
        return \CPasswordHelper::hashPassword($password);
    }
}
