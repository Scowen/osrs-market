<?php

    namespace application\components\http;

    use \Yii;
    use \CException;

    class DbSession extends \CDbHttpSession
    {

        /**
         * @access public
         * @var string $serializer
         * The name of the serialize handler to use.
         */
        public $serializer;

        /**
         * Constructor
         *
         * @access public
         * @return void
         */
        public function init()
        {
            $this->setSerializeHandler($this->serializer);
            // Run the constructor of the parent class(es), if they have any. This is to prevent any compatibility
            // issues with later version of the parent class that get released.
            foreach(class_parents($this) as $parent) {
                if(method_exists($parent, 'init')) {
                    // We don't need to worry about constructor parameters since application components by nature don't
                    // have any.
                    parent::init();
                }
            }
        }

        /**
         * Set: Serialize Handler
         *
         * @access protected
         * @throws \CException
         * @param string $serializer "The name of the serialize handler to set."
         * @return void
         */
        protected function setSerializeHandler($serializer)
        {
            if(is_string($serializer)) {
                if(@ini_set('session.serialize_handler', $serializer) === false) {
                    throw new CException(
                        Yii::t(
                            'application',
                            'CDbHttpSession.serializer "{serializer}" is invalid. Please make sure this version of PHP has been compiled with the serialize handler specified.',
                            array(
                                '{serializer}' => $serializer,
                            )
                        )
                    );
                }
            }
        }

    }
