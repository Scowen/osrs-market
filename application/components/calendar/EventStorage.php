<?php

    namespace application\components\calendar;

    use \CalendR\Event\EventInterface;
    use \application\components\calendar\interfaces\Collection as CollectionInterface;
    use \application\components\calendar\interfaces\EventStorage as EventStorageInterface;

    class EventStorage implements EventStorageInterface
    {

        /**
         * @static
         * @access protected
         * @var array $eventRepository
         */
        protected static $eventRepository = array();

        /**
         * @access protected
         * @var array $events
         */
        protected $events = array();

        /**
         * @access protected
         * @var array $data
         */
        protected $data = array();

        /**
         * Constructor
         *
         * @access public
         * @param array $events
         * @return void
         */
        public function __construct(array $events = array())
        {
            $this->attachSet($events);
        }

        /**
         * Attach Event
         *
         * @access public
         * @param EventInterface $event
         * @param mixed $data
         * @return EventInterface
         */
        public function attach(EventInterface $event, $data = null)
        {
            // Update the event to the correct, original object from the repository. This adds the event to the
            // repository if it has not already been registered.
            $event = self::updateEvent($event);
            // If the event is not in the currect storage, add it now.
            if(!$this->contains($event)) {
                $this->events[$event->getUid()] = $event;
            }
            // Update the custom data associated with this event if any has been specified. We don't send NULL here
            // because NULL would unset any associated data.
            if($data !== null) {
                $this->setData($event, $data);
            }
            // Return the correct, original event object.
            return $event;
        }

        /**
         * Attach Set of Events
         *
         * @access public
         * @param array<EventInterface> $events
         * @return void
         */
        public function attachSet(array $events)
        {
            foreach($events as $event) {
                if($event instanceof EventInterface) {
                    $this->attach($event);
                }
            }
        }

        /**
         * Attach Collection of Events
         *
         * @access public
         * @param CollectionInterface $events
         * @return void
         */
        public function attachCollection(CollectionInterface $events)
        {
            foreach($events as $event) {
                $this->attach($event);
            }
        }

        /**
         * Detach Event
         *
         * @access public
         * @param EventInterface $event
         * @return void
         */
        public function detach(EventInterface $event)
        {
            unset(
                $this->events[$event->getUid()],
                $this->data[$event->getUid()]
            );
        }

        /**
         * Detach All except Event
         *
         * @access public
         * @param EventInterface $event
         * @return void
         */
        public function detachExcept(EventInterface $event)
        {
            $this->detachExceptSet(array($event));
        }

        /**
         * Detach Set of Events
         *
         * @access public
         * @param array<EventInterface> $events
         * @return void
         */
        public function detachSet(array $events)
        {
            foreach($events as $event) {
                if($event instanceof EventInterface) {
                    $this->detach($event);
                }
            }
        }

        /**
         * Detach All except Set of Events
         *
         * @access public
         * @param array<EventInterface> $events
         * @return void
         */
        public function detachExceptSet(array $events)
        {
            array_walk($events, function(&$event, $index) {
                $event = $event instanceof EventInterface
                    ? $event->getUid()
                    : null;
            });
            array_filter($events);
            foreach($this->events as $key => $event) {
                if(!in_array($key, $events)) {
                    $this->detach($event);
                }
            }
        }

        /**
         * Detach Collection of Events
         *
         * @access public
         * @param CollectionInterface $events
         */
        public function detachCollection(CollectionInterface $events)
        {
            foreach($events as $event) {
                $this->detach($event);
            }
        }

        /**
         * Detach All except Collection of Events
         *
         * @access public
         * @param CollectionInterface $events
         * @return void
         */
        public function detachExceptCollection(CollectionInterface $events)
        {
            $this->detachExceptSet($events->all());
        }

        /**
         * Contains Events?
         *
         * @access public
         * @param EventInterface $event
         * @return boolean
         */
        public function contains(EventInterface $event)
        {
            return isset($this->events[$event->getUid()]);
        }

        /**
         * Get: Associated Event Data
         *
         * @access public
         * @param EventInterface $event
         * @return mixed
         */
        public function getData(EventInterface $event)
        {
            return $this->data[$event->getUid()];
        }

        /**
         * Set: Associated Event Data
         *
         * @access public
         * @param EventInterface $event
         * @param mixed $data
         * @return void
         */
        public function setData(EventInterface $event, $data = null)
        {
            if($data === null) {
                unset($this->data[$event->getUid()]);
                return;
            }
            $this->data[$event->getUid()] = $data;
        }

        /**
         * Get: Event
         *
         * Fetch an event from the repository using it's ID.
         *
         * @static
         * @access public
         * @param integer $id
         * @return EventInterface
         */
        public static function getEvent($id)
        {
            return self::$eventRepository[$id];
        }

        /**
         * Update Event
         *
         * @static
         * @access public
         * @param EventInterface $event
         * @return EventInterface
         */
        public static function updateEvent(EventInterface $event)
        {
            if(isset(self::$eventRepository[$event->getUid()])) {
                $event = self::$eventRepository[$event->getUid()];
            }
            else {
                self::$eventRepository[$event->getUid()] = $event;
            }
            return $event;
        }

        /**
         * Refresh Events
         *
         * @access public
         * @return void
         */
        public function refreshEvents()
        {
            foreach($this->events as &$event) {
                $event = self::updateEvent($event);
            }
        }

        /**
         * ArrayAccess: Offset Exists?
         *
         * @access public
         * @param scalar $offset
         * @return boolean
         */
        public function offsetExists($offset)
        {
            return isset($this->events[$offset]);
        }

        /**
         * ArrayAccess: Get Offset
         *
         * @access public
         * @param scalar $offset
         * @return EventInterface
         */
        public function offsetGet($offset)
        {
            return $this->events[$offset];
        }

        /**
         * ArrayAccess: Set Offset
         *
         * @access public
         * @throws BadMethodCallException
         * @param scalar $offset
         * @param mixed $data
         * @return void
         */
        public function offsetSet($offset, $data)
        {
            throw new \BadMethodCallException('Cannot add event through ArrayAccess. Please attach it instead.');
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
            $event = self::getEvent($offset);
            if($event instanceof EventInterface) {
                $this->detach($event);
            }
        }

        /**
         * Countable: Count Events
         *
         * @access public
         * @return integer
         */
        public function count()
        {
            return count($this->events);
        }

        /**
         * IteratorAggregate: Get Iterator
         *
         * @access public
         * @return ArrayIterator
         */
        public function getIterator()
        {
            return new \ArrayIterator($this->events);
        }

        /**
         * Serializable: Serialize
         *
         * @access public
         * @return string
         */
        public function serialize()
        {
            return serialize($this->events);
        }

        /**
         * Serializable: Unserialize
         *
         * @access public
         * @return void
         */
        public function unserialize($serialised)
        {
            $events = unserialize($serialised);
            if(is_array($events)) {
                $this->attachSet($events);
            }
        }

        /**
         * Return All Events as Array
         *
         * @access public
         * @return array<EventInterface>
         */
        public function toArray()
        {
            return $this->events;
        }

    }
