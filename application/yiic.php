<?php

    // The following paths represent where to find Composer's Autoloader, the main application configuration file, and
    // the Yii Framework bootstrap file.
    $composer   = dirname(__FILE__) . '/vendor/autoload.php';
    $yiic       = dirname(__FILE__) . '/vendor/yiisoft/yii/framework/yiic.php';
    $config     = dirname(__FILE__) . '/config/console.php';
    $env        = dirname($config)  . '/appenv.php';

    // Load Composer before the Yii console application.
    require_once $composer;

    // Require the configuration file that sets the application environment.
    require_once $env;

    // Require the Yii Console bootstrap file.
    require_once $yiic;
