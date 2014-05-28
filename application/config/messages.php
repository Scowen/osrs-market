<?php

    return array(

        // The root of the project (note that this is the directory below the application).
        'sourcePath' => dirname(__FILE__) . '/..',
        // Where the messge files should be placed.
        'messagePath' => dirname(__FILE__) . '/../messages',
        // The list of languages that message files should be created for.
        'languages' => array(),
        // What file types should be scanned?
        'fileTypes' => array('php'),
        // Yii already has translation for itself, so exclude the framework directory.
        'exclude' => array('/vendor'),

        // These are the defaults, but specified here as a reference should you wish to change them at a later date.
        'translator' => 'Yii::t',
        'overwrite' => true,
        'removeOld' => false,
        'sort' => false,

    );
