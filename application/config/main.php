<?php

    use Symfony\Component\OptionsResolver\OptionsResolverInterface;

    // Set the date timezone so we don't keep getting all those annoying warnings.
    date_default_timezone_set('Europe/London');

    /* ======================= *\
    |  Set Application Aliases  |
    \* ======================= */

    // Set an alias to the new themes directory.
    Yii::setPathOfAlias('themes', dirname(__FILE__) . '/../themes');
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

    /* ======================== *\
    |  Composer Package Manager  |
    \* ======================== */

    // Require Composer's autoloader to use the packages that this project has a dependency on.
    require_once Yii::getPathOfAlias('composer') . '/autoload.php';

    /* ============================== *\
    |  Main Application Configuration  |
    \* ============================== */

    // This is the main Web application configuration. Any writable CWebApplication properties can be configured here.
    $config = array(
        'basePath' => dirname(__FILE__) . '/..',
        // Do not wrap the application name in Yii's translation method as the preferred language has not been
        // determined yet. Plus we do not want it to change (cryptographic keys use the application name as one of their
        // seeds).
        'name' => 'System60',
        'sourceLanguage' => 'en',
        'theme' => ENVIRONMENT,
        'defaultController' => 'home',
        'controllerNamespace' => '\\application\\controllers',

        // Preloading 'log' component.
        'preload' => array('log'),

        // Autoloading model and component classes. Hopefully this will eventually become obsolete by making use of
        // namespaces in all classes apart from controllers.
        'import' => array(
            'application.components.*',
        ),

        'modules' => array(
            'gii' => array(
                'class' => 'system.gii.GiiModule',
                // If removed, Gii defaults to localhost only. Edit carefully to taste.
                'ipFilters'=>array('127.0.0.1', '::1'),
                'password' => false,
            ),
            'forum' => array(
                'defaultController' => 'board',
            ),
        ),

        // Application components.
        'components' => array(

            // Application Component: Asset Manager.
            'assetManager' => array(
                'class' => '\\application\\components\\assets\\Manager',
            ),

            // Application Component: Asset Publisher.
            'assetPublisher' => array(
                'class' => '\\application\\components\\assets\\Publisher',
                'linkAssets' => true,
            ),

            // Application Component: Authorisation Manager.
            'authManager' => array(
                'class' => 'application\\components\\auth\\Manager',
            ),

            // System Component: Cache.
            'cache' => array(
                'class' => 'system.caching.CDummyCache',
            ),

            // System Component: Database.
            'db' => CMap::mergeArray(
                // This array should contain all the default settings for the database component, ready to be
                // overwritten by application environment specific credentials that are defined inside the database
                // configuration file.
                array(
                    'charset' => 'utf8',
                    'class' => 'system.db.CDbConnection',
                    'emulatePrepare' => true,
                    'enableProfiling' => true,
                ),
                // Pull the database credentials, and other settings (such as table prefix), for the current application
                // environment from the database configuration file and use them to override the default settings.
                $databases[ENVIRONMENT]
            ),

            // System Component: Error Handler.
            'errorHandler' => array(
                'class' => 'system.base.CErrorHandler',
                // Use 'error/index' action to display errors.
                'errorAction' => 'error/index',
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

            'markdown' => array(
                'class' => '\\application\\components\\Markdown',
            ),

            // Application Component: HTTP Request.
            'request' => array(
                'class' => 'application\\components\\http\\Request',
                'enableCookieValidation' => true,
            ),

            // Application Component: HTTP Database Session.
            'session' => array(
                'autoStart' => true,
                'class' => '\\application\\components\\http\\DbSession',
                'connectionID' => 'db',
                'cookieMode' => 'only',
                'cookieParams' => array(
                    'path'      => '/',
                    'secure'    => false,
                    'httponly'  => true,
                ),
                //'serializer' => 'php_binary',
                'sessionName' => 'system60',
                'sessionTableName' => '{{sessions}}',
                'timeout' => 64800,
            ),

            // Application Component: Configurable Settings.
            'settings' => array(
                'class' => '\\application\\components\\settings\\DbSettings',
                'connectionID' => 'db',
                'defaultSettings' => array(
                    // Note, if you specify a setting here and register it in OptionsResolverInterface::setRequired(),
                    // it automatically becomes optional (if not defined the default will be used as a fallback).
                    'calendar.default' => 'month',
                    'calendar.first_weekday' => \CalendR\Period\Day::MONDAY,
                    'calendar.day_begin_hour' => 8,
                    'calendar.day_end_hour' => 17,
                    'login.throttle' => 0.5,
                ),
                'settingsConfiguration' => function(OptionsResolverInterface $resolver) {
                    // Configure required and optional settings.
                    $resolver->setRequired(array(
                        'calendar.default',
                        'calendar.first_weekday',
                        'calendar.day_begin_hour',
                        'calendar.day_end_hour',
                    ));
                    $resolver->setOptional(array(
                        'login.throttle',
                    ));
                    // Configure allowed values.
                    $resolver->setAllowedValues(array(
                        'calendar.default' => array('month', 'week', 'day'),
                    ));
                    // Configure allowed types.
                    $resolver->setAllowedTypes(array(
                        'calendar.first_weekday' => 'integer',
                        'calendar.day_begin_hour' => 'integer',
                        'calendar.day_end_hour' => 'integer',
                    ));
                },
            ),

            // System Component: Theme Manager.
            'themeManager' => array(
                'basePath' => Yii::getPathOfAlias('themes'),
                'class' => 'system.web.CThemeManager',
            ),

            // System Component: URL Manager.
            'urlManager' => array(
                'appendParams' => false,
                'class' => 'system.web.CUrlManager',
                'rules' => require dirname(__FILE__) . '/routes.php',
                'showScriptName' => false,
                'urlFormat' => 'path',
            ),

            // Application Component: Web User.
            'user' => array(
                // Enable cookie-based authentication.
                'class' => '\\application\\components\\user\\WebUser',
                'loginUrl' => array('/login'),
            ),

        ),

        // application-level parameters that can be accessed
        // using Yii::app()->params['paramName']
        'params' => array(
            // this is used in contact page
            'adminEmail' => 'webmaster@example.com',
            // Define the minimum amount of time allowed between login attempts.
            'login.throttle' => 0.5,
        ),
    );

    // Define the "application" namespace, as it doesn't currently exist (CWebApplication doesn't know what the
    // basePath is yet).
    Yii::setPathOfAlias('application', $config['basePath']);

    /* ================================== *\
    |  Environment-specific Configuration  |
    \* ================================== */

    if(file_exists($envConfig = dirname(__FILE__) . '/environments/' . ENVIRONMENT . '.php')) {
        $envConfig = require_once $envConfig;
        if(is_array($envConfig) && !empty($envConfig)) {
            $config = CMap::mergeArray($config, $envConfig);
        }
    }

    return $config;
