<?php

    // Define an empty array to hold all the configuration returned from all the addons.
    $addonConfig = array();
    // Because the main application configuration has not been defined yet, neither have Yii namespaces; determine the
    // addons directory manually. Now search for all directories within "application.addons" that contain a config file,
    // and iterate over them.
    foreach(glob(dirname(__FILE__) . '/../addons/*/config.php') as $addonConfigFile) {
        // Require the addon configuration file to perform initialisation tasks, and capture it's return value.
        $thisConfig = require_once $addonConfigFile;
        // Merge that return value into the configuration array, if one has been returned.
        if(is_array($thisConfig)) {
            $addonConfig = CMap::mergeArray($addonConfig, $thisConfig);
        }
    }

    // Return the configuration array so that it can be merged into the main application configuration.
    return $addonConfig;
