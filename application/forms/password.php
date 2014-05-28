<?php

    return array(
        'title' => Yii::t('application', 'Update your account password.'),

        'elements' => array(
            'old' => array(
                'type' => 'password',
                'hint' => Yii::t('application', 'Please enter your current password.'),
            ),
            'new' => array(
                'type' => 'password',
                'hint' => Yii::t('application', 'Please enter your new password.'),
            ),
            'confirm' => array(
                'type' => 'password',
                'hint' => Yii::t('application', 'Please repeat your new password.'),
            ),
        ),

        'buttons' => array(
            'submit' => array(
                'type' => 'submit',
                'label' => Yii::t('application', 'Change Password'),
            ),
        ),
    );
