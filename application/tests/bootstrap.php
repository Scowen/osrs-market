<?php

    $composer   = dirname(__FILE__) . '/../vendor/autoload.php';
    $yiit       = dirname(__FILE__) . '/../vendor/yiisoft/yii/framework/yiit.php';
    $config     = dirname(__FILE__) . '/../config/test.php';
    $env        = dirname($config)  . '/appenv.php';

    // Load Composer before the Yii application.
    require_once $composer;

    // Require the configuration file that sets the application environment.
    require_once $env;

    // Require the Yii Framework bootstrap file.
    require_once $yiit;
    // Require the WebTestCase, so that we can set up the testing environment (URLs, Browsers, etc).
    require_once dirname(__FILE__) . '/WebTestCase.php';

    // And create our web application (but do not run it).
    Yii::createWebApplication($config);
