<?php

    namespace application\controllers;

    use \Yii;
    use \CException;
    use \application\components\Controller;
    use \application\model\db\User;

    class LogoutController extends Controller
    {

        /**
         * Action: Index
         *
         * @access public
         * @return void
         */
    	public function actionIndex()
    	{
            // Only go ahead with the logout if the end-user is logged in, if the end-user is a guest then we can skip
            // the entire logout procedure, and skip to the redirect back home!
            if(!Yii::app()->user->isGuest) {
                // Has the "authId" persistent state been set, which contains the user's true authenticated ID, and is
                // it different to the user's current ID?
                if(
                    is_int(Yii::app()->user->getState('authId'))
                 && Yii::app()->user->getState('authId') != Yii::app()->user->getState('id')
                 && is_object($authId = User::model()->findByPk(Yii::app()->user->getState('authId')))
                ) {
                    $model = new \application\model\form\Logout;
                    $form = new CForm('application.forms.logout', $model);
                    if($form->submitted() && $form->validate()) {
                        // Has the user opted to switch back to their original account?
                        if($model->switch) {
                            // Create a new user identity instance, but since we aren't going to use any credentials to
                            // authenticate the user, we can just pass in empty strings.
                            $identity = new UserIdentity('', '');
                            // Attempt to load the identitiy of the user defined by the ID in the "authId" persistent
                            // state.
                            if(
                                $identity->load(Yii::app()->user->getState('authId'))
                             && Yii::app()->user->login($identity)
                            ) {
                                // The original user account was successfully logged in, redirect to the homepage.
                                $this->redirect(Yii::app()->homeUrl);
                            }
                            // If we couldn't load the user identity, it means that something went terribly wrong at
                            // some point (since we managed to load the original account through the User active record
                            // earlier). Log the user out completely, just in case, and throw an exception explaining
                            // that we have no fucking clue as to what happened (but obviously a little more polite).
                            else {
                                throw new CException(
                                    Yii::t(
                                        'application',
                                        'An error occured whilst attempting to load your original user account. You have been logged out for security reasons.'
                                    )
                                );
                            }
                        }
                        // If the user did not opt to switch back to their original account, then log them out
                        // completely of both users.
                        else {
                            // Log out the current user. Pass bool(false) as an argument to prevent the entire session
                            // from being destroyed, and instead only delete the persistent states that were created
                            // specifically for the user logged in. An example of why this is the behaviour we want is
                            // storing the language selection in a session - if the website visitor selects French as
                            // the language to view the website in (regardless of whether this functionality is actually
                            // implemented), we don't want that session variable destroyed causing the entire website to
                            // switch back to the default language of English just because the user wanted to log out.
                            // Personally I believe that this should be the default behaviour, but it's not.
                            Yii::app()->user->logout(false);
                            $this->redirect(Yii::app()->homeUrl);
                        }
                    }
                    // Render the logout page, so that the end-user may be presented with a choice instead of
                    // automatically logged out (as if the default).
                    $this->render('index', array(
                        'form' => $form,
                        'displayName' => $authId->displayName,
                    ));
                    // End the application here to prevent any redirection.
                    Yii::app()->end();
                }
                // Either the "authId" persistent state variable was not a user ID, or was the same user ID as the
                // authenticated user (meaning that they weren't emulating another user). Log them out of the system.
                else {
                    // Log out the current user. Please refer to the lengthy explaination earlier in this method for why
                    // we pass bool(false).
                    Yii::app()->user->logout(false);
                }
            }
            // We don't want the user to stay on the logout page. Redirect them back to the homepage.
            $this->redirect(Yii::app()->homeUrl);
    	}

    }
