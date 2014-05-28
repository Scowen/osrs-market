<?php

    namespace application\components\calendar;

    use \Yii;
    use \CException as Exception;
    use \CalendR\Event\EventInterface;
    use \CalendR\Period\PeriodInterface;
    use \application\components\calendar\interfaces\Collection as CollectionInterface;

    class Collection implements CollectionInterface
    {

        /**
         * @access protected
         * @var EventStorage $events
         */
        protected $events;

        /**
         * Constructor
         *
         * @access public
         * @param array $events
         * @return void
         */
        public function __construct(array $events = array())
        {
            $this->events = new EventStorage;
            $this->events->attachSet($events);
        }

        /**
         * Adds an event to the collection
         *
         * @access public
         * @param EventInterface $event
         * @return void
         */
        public function add(EventInterface $event)
        {
            $this->events->attach($event);
        }

        /**
         * Add Set of Events
         *
         * @access public
         * @param array<EventInterface>
         * @return void
         */
        public function addSet(array $events)
        {
            $this->events->attachSet($events);
        }

        /**
         * Add Collection of Events
         *
         * @access public
         * @param CollectionInterface $collection
         * @return void
         */
        public function addCollection(CollectionInterface $collection)
        {
            $this->events->attachCollection($collection);
        }

        /**
         * Remove Event
         *
         * @access public
         * @param EventInterface $event
         * @return void
         */
        public function remove(EventInterface $event)
        {
            $this->events->detach($event);
        }

        /**
         * Remove Set
         *
         * Remove All Events in Array from Collection
         *
         * @access public
         * @param array<EventInterface> $events
         * @return void
         */
        public function removeSet(array $events)
        {
            $this->events->detachSet($events);
        }

        /**
         * Remove Collection of Events
         *
         * @access public
         * @param CalendR\Event\Collection\CollectionInterface $collection
         * @return void
         */
        public function removeCollection(CollectionInterface $collection)
        {
            $this->events->detachCollection($collection);
        }

        /**
         * Return Events
         *
         * Return all events as an array.
         *
         * @access public
         * @return array<EventInterface>
         */
        public function all()
        {
            return $this->events->toArray();
        }

        /**
         * Has Event?
         *
         * Returns if there is events corresponding to $index period
         *
         * @access public
         * @param mixed $index
         * @return boolean
         */
        public function has($index)
        {
            return count($this->find($index, false)) > 0;
        }

        /**
         * Find Events
         *
         * Find events in the collection corresponding to the $index period.
         *
         * @access public
         * @param mixed $index
         * @param boolean $collection
         * @return CollectionInterface|array<EventInterface>
         */
        public function find($index, $collection = true)
        {
            $result = array();
            foreach($this->events as $event) {
                if(($index instanceof PeriodInterface && $index->containsEvent($event))
                || ($index instanceof \DateTime && $event->contains($index))
                ) {
                    $result[] = $event;
                }
            }
            return $collection
                ? new self($result)
                : $result;
        }

        /**
         * Countable: Count Events
         *
         * @access public
         * @return integer
         */
        public function count()
        {
            return $this->events->count();
        }

        /**
         * IteratorAggregate: Get Iterator
         *
         * @access public
         * @return IteratorIterator
         */
        public function getIterator()
        {
            return new \IteratorIterator($this->events);
        }

    }
