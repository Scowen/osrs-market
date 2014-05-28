<?php

    function getHost()
    {
        // Determine the "host" (domain) part of the requested resource URL.
        if(isset($_SERVER['HTTP_X_FORWARDED_HOST']) && $host = $_SERVER['HTTP_X_FORWARDED_HOST']) {
            $elements = explode(',', $host);
            $host = trim(end($elements));
        }
        else {
            if(!isset($_SERVER['HTTP_HOST']) || !$host = $_SERVER['HTTP_HOST']) {
                if(!isset($_SERVER['SERVER_NAME']) || !$host = $_SERVER['SERVER_NAME']) {
                    $host = isset($_SERVER['SERVER_ADDR']) && !empty($_SERVER['SERVER_ADDR'])
                        ? $_SERVER['SERVER_ADDR']
                        : '';
                }
            }
        }
        // Remove port number and username from host.
        return trim(preg_replace('/(^.+@|:\d+$)/', '', $host));
    }

    // Define the Perl-compatible Regular Expression for valid labels in PHP, as application environments must adhere to 
    // this rule.
    defined('VALIDLABEL') || define('VALIDLABEL', '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/');
    // Specify that we are in production mode, we can turn it off in the front controller if you want.
    defined('PRODUCTION') || define('PRODUCTION', true);
    // If the application environment has already been defined, return it straight away.
    // First check that the ENVIRONMENT constant has not already been set in the front-controller (useful for overriding 
    // configuration values in development or testing stages). If so, return it straight away.
    if(defined('ENVIRONMENT')) {
        return ENVIRONMENT;
    }

    /* ================ *\
    |  VIA: HOST/DOMAIN  |
    \* ================ */

    // Calculate the application environment from the current domain if it has been determined from server variables.
    if(!empty($host = getHost())) {
        // Load up the configuration for mapping hosts to application environments.
        $hosts = file_exists($hostsConfig = dirname(__FILE__) . '/hosts.php')
            ? require_once $hostsConfig
            : null;
        // Check to see if the current domain is in the "hosts" configuration.
        if(is_array($hosts) && !empty($hosts)) {
            foreach($hosts as $domain => $appenv) {
                if($domain === $host) {
                    if(is_string($appenv) && preg_match(VALIDLABEL, $appenv)) {
                        define('ENVIRONMENT', strtolower($appenv));
                        return ENVIRONMENT;
                    }
                }
            }
        }
    }
    
    /* ==================== *\
    |  VIA: SERVER VARIABLE  |
    \* ==================== */
    
    // Check if the application environment has been set in the server variables. This can be done through Apache's 
    // htaccess files, or through the Nginx server configuration.
    if(isset($_SERVER['ENVIRONMENT']) && preg_match(VALIDLABEL, $appenv = trim($_SERVER['ENVIRONMENT']))) {
        define('ENVIRONMENT', strtolower($appenv));
        return ENVIRONMENT;
    }
    
    /* ================ *\
    |  VIA: CONFIG FILE  |
    \* ================ */
    
    $envfile = dirname(__FILE__) . '/.environment';
    if(file_exists($envfile) && preg_match(VALIDLABEL, $appenv = trim(file_get_contents($envfile)))) {
        define('ENVIRONMENT', strtolower($appenv));
        return ENVIRONMENT;
    }
    
    // If we got this far then a suitable application environment could not be found. Throw an exception.
    throw new \Exception('Could not determine a valid application environment. Please check the application and/or server configuration, and try again.');