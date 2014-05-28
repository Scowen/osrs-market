<?php

    namespace application\components\settings;

    use \Yii;
    use \CException as Exception;
    use \CApplicationComponent as ApplicationComponent;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;

    abstract class SettingsResolver extends ApplicationComponent implements \ArrayAccess, \Countable, \Iterator
    {

        /**
         * @access public
         * @var closure $settingsConfiguration
         */
        public $settingsConfiguration;

        /**
         * @access public
         * @var array $defaultSettings
         */
        public $defaultSettings = array();

        /**
         * @access protected
         * @var array $settings
         */
        protected $settings;

        /**
         * @access protected
         * @var OptionsResolver $resolver
         */
        protected $resolver;

        /**
         * @access private
         * @var mixed $current
         */
        private $current;

        /**
         * @access private
         * @var mixed $key
         */
        private $key;

        /**
         * Initialisation
         *
         * @access public
         * @return void
         */
        public function init()
        {
            // Make sure we have the class properties correctly set.
            if(!is_callable($this->settingsConfiguration)) {
                throw new Exception(
                    Yii::t('app', 'Setttings configuration must be the name of a valid callable method or function, or a closure.')
                );
            }
            // Initialise the options resolver and provide it with all the configuration required.
            $this->resolver = new OptionsResolver;
            // Allow the settings resolver to be configured from the main application configuration.
            call_user_func($this->settingsConfiguration, $this->resolver);
            // Fetch the configured settings to load and resolve, after setting the defaults in case not all have been
            // configured.
            if(is_array($this->defaultSettings) && !empty($this->defaultSettings)) {
                $this->resolver->setDefaults($this->defaultSettings);
            }
            $settings = $this->fetchConfiguredSettings();
            // Resolve the configured settings.
            $this->settings = $this->resolver->resolve($settings);
        }

        /**
         * Fetch Configured Settings
         *
         * @abstract
         * @access protected
         * @return array
         */
        abstract protected function fetchConfiguredSettings();

        /**
         * ArrayAccess: Offset Exists?
         *
         * @access public
         * @param scalar $offset
         * @return boolean
         */
        public function offsetExists($offset)
        {
            return isset($this->settings[$offset]);
        }

        /**
         * ArrayAccess: Get Offset
         *
         * @access public
         * @param scalar $offset
         * @return mixed
         */
        public function offsetGet($offset)
        {
            return $this->offsetExists($offset)
                ? $this->settings[$offset]
                : null;
        }

        /**
         * ArrayAccess: Set Offset
         *
         * @access public
         * @param scalar $offset
         * @param mixed $value
         * @return void
         */
        public function offsetSet($offset, $value)
        {
            $this->settings = $this->resolver->resolve(\CMap::mergeArray($this->settings, array($offset => $value)));
        }

        /**
         * ArrayAccess: Unset Offset
         *
         * @access public
         * @param scalar $offset
         * @return void
         */
        public function offsetUnset($offset)
        {
            // This method should revert a setting back to its default value, NOT unset it.
            $settings = $this->resolver->resolve();
            $this->settings = \CMap::mergeArray($this->settings, array($offset => $settings[$offset]));
        }

        /**
         * Countable: Count
         *
         * @access public
         * @return integer
         */
        public function count()
        {
            return count($this->settings);
        }

        /**
         * Iterator: Current
         *
         * @access public
         * @return mixed
         */
        public function current()
        {
            return $this->current;
        }

        /**
         * Iterator: Key
         *
         * @access public
         * @return scalar
         */
        public function key()
        {
            return $this->key;
        }

        /**
         * Iterator: Next
         *
         * @access public
         * @return void
         */
        public function next()
        {
            $this->current = next($this->settings);
            $this->key = key($this->settings);
        }

        /**
         * Iterator: Rewind
         *
         * @access public
         * @return void
         */
        public function rewind()
        {
            $this->current = reset($this->settings);
            $this->key = key($this->settings);
        }

        /**
         * Iterator: Valid
         *
         * @access public
         * @return void
         */
        public function valid()
        {
            return $this->key !== null;
        }

    }
