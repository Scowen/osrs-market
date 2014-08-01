<?php

    return array(
        'elements' => array(
            'username' => array(
                'type' => 'text',
                'maxlength' => 32,
            ),
            'password' => array(
                'type' => 'password',
                'maxlength' => 48,
            ),
            'email' => array(
                'type' => 'email',
                'maxlength' => 128,
            ),
        ),

        'buttons' => array(
            'submit' => array(
                'type' => 'submit',
                'label' => Yii::t('application', 'Register'),
            ),
        ),
    );
