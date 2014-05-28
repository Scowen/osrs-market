<?php

    namespace application\components\calendar\interfaces;

    use \CalendR\Event\EventInterface as EventInterface;
    use \application\components\calendar\interfaces\Collection as CollectionInterface;

    interface Container extends \Countable, \IteratorAggregate
    {

        /**
         * Add Event
         *
         * @abstract
         * @access public
         * @param EventInterface $event
         * @return void
         */
        public function add(EventInterface $event);

        /**
         * Add Set of Events
         *
         * @abstract
         * @access public
         * @param array $events
         * @return void
         */
        public function addSet(array $events);

        /**
         * Add Collection of Events
         *
         * @abstract
         * @access public
         * @param CollectionInterface $collection
         * @return void
         */
        public function addCollection(CollectionInterface $collection);

        /**
         * Remove Event
         *
         * @abstract
         * @access public
         * @param EventInterface $event
         * @return void
         */
        public function remove(EventInterface $event);

        /**
         * Remove Set of Events
         *
         * @abstract
         * @access public
         * @param array $events
         * @return void
         */
        public function removeSet(array $events);

        /**
         * Remove Collection of Events
         *
         * @abstract
         * @access public
         * @param CollectionInterface $collection
         * @return void
         */
        public function removeCollection(CollectionInterface $collection);

    }
