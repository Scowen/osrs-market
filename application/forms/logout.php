<?php

    return array(

        'title' => Yii::t('application', 'Logout'),

        'elements' => array(
            'switch' => array(
                'type' => 'checkbox',
                'label' => Yii::t('application', 'Switch to Original Account?'),
                'hint' => Yii::t(
                    'application',
                    'You are currently impersonating another user\'s account. Would you like to switch back your original account instead of logging out completely?'
                ),
            ),
        ),

        'buttons' => array(
            'submit' => array(
                'type' => 'submit',
                'label' => Yii::t('application', 'Logout'),
            ),
        ),

    );
