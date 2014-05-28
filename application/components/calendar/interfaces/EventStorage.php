<?php

    namespace application\components\calendar\interfaces;

    use \CalendR\Event\EventInterface;
    use \application\components\calendar\interfaces\Collection as CollectionInterface;

    interface EventStorage extends \ArrayAccess, \Countable, \IteratorAggregate, \Serializable
    {

        /**
         * Attach Event
         *
         * @access public
         * @param EventInterface $event
         * @param mixed $data
         * @return EventInterface
         */
        public function attach(EventInterface $event, $data = null);

        /**
         * Attach Set of Events
         *
         * @access public
         * @param array<EventInterface> $events
         * @return void
         */
        public function attachSet(array $events);

        /**
         * Attach Collection of Events
         *
         * @access public
         * @param CollectionInterface $events
         * @return void
         */
        public function attachCollection(CollectionInterface $events);

        /**
         * Detach Event
         *
         * @access public
         * @param EventInterface $event
         * @return void
         */
        public function detach(EventInterface $event);

        /**
         * Detach All except Event
         *
         * @access public
         * @param EventInterface $event
         * @return void
         */
        public function detachExcept(EventInterface $event);

        /**
         * Detach Set of Events
         *
         * @access public
         * @param array<EventInterface> $event
         * @return void
         */
        public function detachSet(array $event);

        /**
         * Detach All except Set of Events
         *
         * @access public
         * @param array<EventInterface> $events
         * @return void
         */
        public function detachExceptSet(array $events);

        /**
         * Detach Collection of Events
         *
         * @access public
         * @param CollectionInterface $events
         * @return void
         */
        public function detachCollection(CollectionInterface $events);

        /**
         * Detach All except Collection of Events
         *
         * @access public
         * @param CollectionInterface $events
         * @return void
         */
        public function detachExceptCollection(CollectionInterface $events);

        /**
         * Contains Event?
         *
         * @access public
         * @param EventInterface $event
         * @return boolean
         */
        public function contains(EventInterface $event);

        /**
         * Get: Event Data
         *
         * @access public
         * @param EventInterface $event
         * @return mixed
         */
        public function getData(EventInterface $event);

        /**
         * Set: Event Data
         *
         * @access public
         * @param EventInterface $event
         * @param mixed $data
         * @return void
         */
        public function setData(EventInterface $event, $data = null);

        /**
         * Get: Event (by ID)
         *
         * @access public
         * @param integer $id
         * @return EventInterface
         */
        public static function getEvent($id);

        /**
         * Update Event Object
         *
         * @access public
         * @param EventInterface $event
         * @return EventInterface
         */
        public static function updateEvent(EventInterface $event);

        /**
         * Refresh Events
         *
         * @access public
         * @return void
         */
        public function refreshEvents();

        /**
         * Return All Events as Array
         *
         * @access public
         * @return array<EventInterface>
         */
        public function toArray();

    }
