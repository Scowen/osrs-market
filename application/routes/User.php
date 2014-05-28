<?php

    namespace application\routes;

    use \Yii;
    use \CException;
    use \application\models\db\user\User as UserModel;

    class User extends \CBaseUrlRule
    {

        /**
         * @var string $connectionID
         * @access public
         */
        public $connectionID = 'db';

        /**
         * @var \CDbConnection $db
         * @access protected
         */
        protected $db;


        /**
         * Get: Database Connection
         *
         * @access  protected
         * @throws  \CException
         * @return  \CDbConnection
         */
        public function getDbConnection()
        {
            if($this->db !== null || ($this->db = Yii::app()->getComponent($this->connectionID)) instanceof \CDbConnection) {
                return $this->db;
            }
            else {
                throw new CException(
                    Yii::t('application', 'The connection ID for the User Route is invalid. Please make sure it refers to the ID of a CDbConnection application component.')
                );
            }
        }


        /**
         * Create URL
         *
         * @access  public
         * @param   \CURLManager    $manager
         * @param   string          $route
         * @param   array           $params
         * @param   string          $ampersand
         * @return  string|false
         */
        public function createUrl($manager, $route, $params, $ampersand)
        {
            if(preg_match('/^profile(\\/[a-zA-Z\\d_]+)?/', $route, $matches)) {
                    if(isset($params['id']) && is_scalar($params['id']) && !empty($params['id'])) {
                        $user = UserModel::model()->findByPk($params['id']);
                        if(is_object($user) && !empty($user->username)) {
                            $action = isset($matches[1]) ? trim($matches[1], '/') : '';
                            return $action === 'index' || $action === ''
                                ? '+' . $user->username
                                : '+' . $user->username . '/' . $action;
                        }
                    }
            }
            return false;
        }


        /**
         * Parse URL
         *
         * @access  public
         * @param   \CURLManager    $manager
         * @param   \CHttpRequest   $request
         * @param   string          $path
         * @param   string          $rawPath
         * @return  string|false
         */
        public function parseUrl($manager, $request, $path, $rawPath)
        {
            $realRawPath = substr($request->requestUri, 0, $len = strlen($baseUrl = rtrim($request->baseUrl, '/') . '/')) === $baseUrl
                ? substr($request->requestUri, $len)
                : $request->requestUri;
            $slug = '[a-zA-Z\\d]+(?:-[a-zA-Z\\d]+)*';
            switch(true) {
                case preg_match('/^\\+([a-zA-Z\\d_]+)((\\/[a-zA-Z\\d_]+)*)$/', $realRawPath, $matches):
                    $user = UserModel::model()->findByAttributes(array('username' => $matches[1]));
                    if(is_object($user)) {
                        $_GET['id'] = $user->id;
                        return empty($matches[2])
                            ? 'profile/index'
                            : 'profile/' . trim($matches[2], '/');
                        return 'profile/index';
                    }
                    break;
            }
            return false;
        }

    }
