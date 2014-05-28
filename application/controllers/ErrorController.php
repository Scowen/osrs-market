<?php

    namespace application\controllers;

    use \Yii;
    use \CException;
    use \application\components\Controller;

    class ErrorController extends Controller
    {

        /**
         * Action: Index
         *
         * This is the action that is used to handle external exceptions.
         *
         * @access public
         * @return void
         */
        public function actionIndex()
        {
            if($error = Yii::app()->errorHandler->error) {
                if(Yii::app()->request->isAjaxRequest) {
                    echo $error['message'];
                }
                else {
                    $this->render('index', $error);
                }
            }
            else {
                $this->render('error');
            }
        }

    }
