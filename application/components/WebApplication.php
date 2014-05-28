<?php

    namespace application\components;

    use \Yii;
    use \CEvent as Event;
    use \CException;
    use \CHttpException;

    class WebApplication extends \CWebApplication
    {

        /**
         * Get: Asset Manager
         *
         * In our application configuration we have renamed Yii's asset manager to asset publisher. For backwards
         * compatibility, override this method to return the correct component in framework files.
         *
         * @access public
         * @return application\components\assets\Publisher
         */
        public function getAssetManager()
        {
            return $this->getComponent('assetPublisher');
        }

        /**
         * Run Controller
         *
         * Creates the controller and performs the specified action.
         *
         * @access  public
         * @throws  \CHttpException
         * @param   string          $route
         * @return  void
         */
        public function runController($route)
        {
            $controllerAction = $this->createController($route);
            if($controllerAction === null) {
                throw new CHttpException(
                    404,
                    Yii::t(
                        'yii',
                        'Unable to resolve the request "{route}".',
                        array(
                            '{route}' => empty($route)
                                ? $this->defaultController
                                : $route,
                        )
                    )
                );
            }
            list($controller, $action) = $controllerAction;
            // Switch in the new controller for the previous one.
            $cachedController = $this->getController();
            $this->setController($controller);
            // Initialise the controller.
            $controller->init();
            // Minor hack.
            $controller->action = $action;
            // Run the controller action, whilst firing before and after events.
            $this->onBeforeControllerAction(new Event($this));
            $controller->run($action);
            $this->onAfterControllerAction(new Event($this));
            // Switch back the previous controller.
            $this->setController($cachedController);
        }


        /* ================= *\
        |  EVENT DEFINITIONS  |
        \* ================= */


        /**
         * Event: Start Authenticate Process
         *
         * @access public
         * @return void
         */
        public function onBeforeControllerAction($event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }


        /**
         * Event: Start Authenticate Process
         *
         * @access public
         * @return void
         */
        public function onAfterControllerAction($event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            // method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }

    }
