<?php

    namespace application\controllers;

    use \Yii;
    use \CException;
    use \application\components\Controller;

    class HomeController extends Controller
    {

        /**
         * Action: Index
         *
         * @access public
         * @return void
         */
        public function actionIndex()
        {
            if(Yii::app()->request->isAjaxRequest)
                $this->renderPartial('index');
            else
                $this->render('index');
        }

    }
