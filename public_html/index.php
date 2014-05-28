<?php

    // The following paths represent where to find Composer's Autoloader, the main application configuration file, and
    // the Yii Framework bootstrap file.

    $app        = dirname(__FILE__) . '/../application';

    $yii        = $app . '/vendor/yiisoft/yii/framework/yii.php';
    $config     = $app . '/config/main.php';
    $env        = dirname($config)  . '/appenv.php';

    /* ========================================== *\
    |  Application Environment and Debug Settings  |
    \* ========================================== */

    // Require the configuration file that sets the application environment (unless it has already been set by another
    // front-controller).
    require_once $env;

    // If we are not in production mode, we want to enable debugging, HTML logging, and backtraces.
    // Note that YII_DEBUG must be defined BEFORE we include the Yii Framework bootstrap file.
    if(!PRODUCTION) {
        headers_sent() || header('Application-Environment: ' . ucwords(ENVIRONMENT));
        defined('YII_DEBUG') || define('YII_DEBUG', true);
        defined('YII_TRACE_LEVEL') || define('YII_TRACE_LEVEL', 3);
    }

    /* ========================================== *\
    |  Yii Framework and Web Application + Config  |
    \* ========================================== */

    // Require the Yii Framework bootstrap file, then set some public configuration options.
    require_once $yii;
    // We DO NOT want Yii to rely on the PHP include path to autoload class files. Leaving it as the default of true
    // will make Yii's autoloader try to include non-namespaced class files without checking if they exist or not. This
    // causes problems when we try to use functions such as class_exists(), is_callable(), etc.
    Yii::$enableIncludePath = false;

    // Load the configuration file now instead of passing the filepath. We want some things loaded right NOW (such as
    // Composer's autoloader) so that namespaces will be resolved.
    $config = require_once $config;

    // Now create our web application using the configuration we just loaded and a customer WebApplication component.
    // After that's done, run the damn thing!
    Yii::createApplication('\\application\\components\\WebApplication', $config)->run();
