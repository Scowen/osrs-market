<?php

    namespace application\controllers;

    use \Yii;
    use \CException as Exception;
    use \application\components\Controller;
    use \application\components\form\Form;
    use \application\components\user\Identity as UserIdentity;

    class LoginController extends Controller
    {

        /**
         * @var CUserIdentity $identity
         */
        protected $identity;

        /**
         * Action: Index
         *
         * @access public
         * @return void
         */
        public function actionIndex()
        {
            // If the end-user is already logged in, then there is absolutely no point presenting the login form to them
            // as it will just create confusion as to whether they are logged in or not. Redirect them to the homepage.
            if(!Yii::app()->user->isGuest) {
                $this->redirect(Yii::app()->homeUrl);
            }
            // Create an instance of the form builder, this controls all of the display logic (inputs).
            // Load up the form configuration from the path alias, and associate it with the form model.
            $form = new Form('application.forms.login', new \application\models\form\Login);
            // Check that the form has been submitted, and if has, if it passes the models validation rules.
            if($form->submitted() && $form->validate()) {
                // Seeing as the end-user has provided valid input data, create a new user identity with it.
                $this->identity = new UserIdentity($form->model->username, $form->model->password);
                // Do the credentials provided by the end-user's input data authenticate them as a valid user?
                if($this->identity->authenticate()) {
                    // Great! The end-user provided correct authentication credentials! Log in the user provided by the
                    // user identity created from those credentials.
                    Yii::app()->user->login($this->identity);
                    // Redirect back to where they were (defaults to the homepage if the location to return to has not
                    // been set).
                    $this->redirect(Yii::app()->user->getReturnUrl(Yii::app()->homeUrl));
                }
                // Unfortunately, they did not manage to pass authentication using the credentials the provided in the
                // input data.
                else {
                    // Log this failed authentication attempt.
                    Yii::log(
                        'User "' . $form->model->username . '" provided incorrect credentials.',
                        'info',
                        'application.controllers.LoginController'
                    );
                    // Grab the error code defined by UserIdentity, and add the appropriate error message to the correct
                    // model attribute (form field), so that it may be rendered by the form builder in the view.
                    switch($this->identity->errorCode) {
                        // The end-user provided a string that does not correspond to any user that we have in the
                        // database.
                        case UserIdentity::ERROR_USERNAME_INVALID:
                            $form->model->addError(
                                'username',
                                Yii::t('application', 'The username you entered does not exist.')
                            );
                            break;
                        // The end-user specified a username that is not allowed to login via the current IP address
                        // that the end-user is using.
                        case UserIdentity::ERROR_IP_INVALID:
                            $form->model->addError(
                                'username',
                                Yii::t('application', 'The username you entered may not login at this IP address.')
                            );
                            break;
                        // The end-user has made too many login attempts in a specified amount of time, inform the user
                        // to wait a while before the next attempt.
                        case UserIdentity::ERROR_THROTTLED:
                            $form->model->addError(
                                'username',
                                Yii::t('application', 'The username you entered has been throttled for security reasons. Please try again after a couple of seconds.')
                            );
                            break;
                        // The end-user has specified a password that does not match the one associated with the
                        // username the end-user provided.
                        case UserIdentity::ERROR_PASSWORD_INVALID:
                            $form->model->addError(
                                'password',
                                Yii::t('application', 'The password you entered was incorrect.')
                            );
                            break;
                    }
                }
            }
            else {
                // The login form has either not been submitted, or there were validation errors. Either way we don't
                // need to do anything, other than to carry on as normal to render the form. Yii will deal with all the
                // error messages for us :)
            }
            // Make sure that we do not auto-populate the password field with anything that may have been submitted by
            // the user; we do not want their password encoded into the HTML of our page.
            $form->model->password = null;
            // Pass the form builder to the default login view to render the login HTML form. If it is an AJAX request,
            // render only the partial "ajax" view.
            Yii::app()->request->isAjaxRequest
                ? $this->renderPartial('ajax', array('form' => $form))
                : $this->render('index', array('form' => $form));
        }


        /**
         * Action: Switch
         *
         * @access public
         * @return void
         */
        public function actionSwitch($id)
        {
        }

    }
