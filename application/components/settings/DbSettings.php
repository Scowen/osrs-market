<?php

    namespace application\components\settings;

    use \Yii;
    use \CException as Exception;

    class DbSettings extends SettingsResolver
    {

        /**
         * @access public
         * @var string $connectionID
         */
        public $connectionID = 'db';

        /**
         * Fetch Configured Settings
         *
         * @access protected
         * @return array
         */
        protected function fetchConfiguredSettings()
        {
            return array();
        }

    }
