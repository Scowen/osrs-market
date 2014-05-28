<?php

    namespace application\models\form;

    use \Yii;
    use \CException;
    use \application\components\form\Model as FormModel;

    /**
     * LoginForm: Form Model
     *
     * LoginForm is the model for defining how the HTML login form is used.
     */
    class Login extends FormModel
    {

        /**
         * @var string $username
         */
        public $username;

        /**
         * @var string $password
         */
        public $password;


        /**
         * Validation Rules
         *
         * Defines the validation rules for the form inputs.
         *
         * @access public
         * @return array
         */
        public function rules()
        {
            return array(
                // Username and password are required.
                array('username, password', 'required'),
                // The database has a maximum username length of 64 characters.
                array('username', 'length', 'max' => 64),
            );
        }


        /**
         * Attribute Labels
         *
         * @access public
         * @return array
         */
        public function attributeLabels()
        {
            return array(
                'username' => Yii::t('application', 'Username'),
                'password' => Yii::t('application', 'Password'),
            );
        }

    }
