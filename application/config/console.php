<?php

    // Set the date timezone so we don't keep getting all those annoying warnings.
    date_default_timezone_set('Europe/London');

    /* ======================= *\
    |  Set Application Aliases  |
    \* ======================= */

    // Set a Yii alias to the composer packages directory.
    Yii::setPathOfAlias('composer', realpath(dirname(__FILE__) . '/../vendor'));

    /* ======================== *\
    |  Set Database Credentials  |
    \* ======================== */

    // Require the database credentials configuration. Do not use require_once because the database credentials may be
    // needed elsewhere in the application; it does not include any code that would cause an error if included again.
    $databases = require dirname(__FILE__) . '/databases.php';
    if(!isset($databases[ENVIRONMENT]) || !is_array($databases[ENVIRONMENT])) {
        throw new CDbException(
            Yii::t(
                'application',
                'Could not select the correct database credentials; please verify the application environment and database definitions.'
            )
        );
    }

    /* ============================== *\
    |  Main Application Configuration  |
    \* ============================== */

    // This is the configuration for yiic console application. Any writable CConsoleApplication properties can be
    // configured here.
    return array(
        'basePath' => dirname(__FILE__) . '/..',
        'name' => Yii::t('application', 'New Project'),
        'sourceLanguage' => 'en',

        // Preloading 'log' component.
        'preload' => array('log'),

        // Command Map.
        'commandMap' => array(
            'migrate' => array(
                'class' => 'system.cli.commands.MigrateCommand',
                'migrationPath' => 'application.migrations',
                'migrationTable' => '{{migration}}',
                'connectionID' => 'db',
                'interactive' => false,
            ),
            'merge' => array(
                'class' => '\\application\\commands\\MergeCommand',
                'mergePath' => 'application.migrations.merges',
                'mergeTable' => '{{merge}}',
                'interactive' => false,
            ),
        ),

        // application components
        'components' => array(

            // System Component: Database.
            'db' => CMap::mergeArray(
                // This array should contain all the default settings for the database component, ready to be
                // overwritten by application environment specific credentials that are defined inside the database
                // configuration file.
                array(
                    'charset' => 'utf8',
                    'class' => 'system.db.CDbConnection',
                    'emulatePrepare' => true,
                ),
                // Pull the database credentials, and other settings (such as table prefix), for the current application
                // environment from the database configuration file and use them to override the default settings.
                $databases[ENVIRONMENT]
            ),

            // System Component: Log.
            'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    array(
                        'class' => 'system.logging.CFileLogRoute',
                        'levels' => 'error, warning',
                    ),
                ),
            ),

        ),
    );
