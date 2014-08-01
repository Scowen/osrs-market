<?php

    namespace application\controllers;

    use \Yii;
    use \CException;
    use \application\components\Controller;

    class ItemController extends Controller
    {

        /**
         * Action: Index
         *
         * @access public
         * @return void
         */
        public function actionUpdate()
        {
            $zybez = new \application\components\Zybez;
            $zybez = $zybez->updateAllItems();

            $variables = array(
                'zybez' => $zybez,
            );

            if(Yii::app()->request->isAjaxRequest)
                $this->renderPartial('update', $variables);
            else
                $this->render('update', $variables);
        }

        public function actionView($id = null)
        {
            $item = \application\models\db\Items::model()->findByPk($id);

            $variables = array(
                'item' => $item,
            );

            if(Yii::app()->request->isAjaxRequest)
                $this->renderPartial('view', $variables);
            else
                $this->render('view', $variables);
        }

        public function actionAdd($string)
        {
            $zybez = new \application\components\Zybez;
            $zybez = $zybez->updateItem($string);

            $variables = array(
                'zybez' => $zybez,
            );

            if(Yii::app()->request->isAjaxRequest)
                $this->renderPartial('update', $variables);
            else
                $this->render('update', $variables);
        }

    }
