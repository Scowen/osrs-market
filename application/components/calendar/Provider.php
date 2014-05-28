<?php

    namespace application\components\calendar;

    use \Yii;
    use \CException as Exception;
    use \CalendR\Event\EventInterface;
    use \application\components\calendar\interfaces\Provider as ProviderInterface;
    use \application\components\calendar\interfaces\Collection as CollectionInterface;
    use \application\components\calendar\ObjectStorage;

    class Provider implements ProviderInterface
    {

        /**
         * @access protected
         * @var array<EventInterface> $events
         */
        protected $events;

        /**
         * Constructor
         *
         * @access public
         * @param array<EventInterface> $events
         * @return void
         */
        public function __construct(array $events = array())
        {
            $this->events = new EventStorage;
            $this->events->attachSet($events);
        }

        /**
         * Get: Events
         *
         * Get all the events that fall within the specified dates.
         * @{inheritDoc}
         */
        public function getEvents(\DateTime $begin, \DateTime $end, array $options = array())
        {
            $events = array();
            foreach($this->events as $event) {
                if($event->contains($begin) || $event->contains($end)
                || (1 === $event->getBegin()->diff($begin)->invert && 0 === $event->getEnd()->diff($end)->invert)
                ) {
                    $events[] = $event;
                }
            }
            return $events;
        }

        /**
         * Get: Events as Collection
         *
         * Get all the events that fall within the specified dates.
         * @{inheritDoc}
         */
        public function getEventCollection(\DateTime $begin, \DateTime $end, array $options = array())
        {
            $events = array();
            foreach($this->events as $event) {
                if($event->contains($begin) || $event->contains($end)
                || (1 === $event->getBegin()->diff($begin)->invert && 0 === $event->getEnd()->diff($end)->invert)
                ) {
                    $events[] = $event;
                }
            }
            return new Collection($events);
        }

        /**
         * Add Event
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
         * @param array<EventInterface> $events
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
         * Remove Set of Events
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
         * @param CollectionInterface $collection
         * @return void
         */
        public function removeCollection(CollectionInterface $collection)
        {
            $this->events->detachCollection($collection);
        }

        /**
         * Return All Events
         *
         * @access public
         * @return array<EventInterface>
         */
        public function all()
        {
            return $this->events->toArray();
        }

        /**
         * Get: Iterator
         *
         * @access public
         * @return \Traversable
         */
        public function getIterator()
        {
            return new \IteratorIterator($this->events);
        }

        /**
         * Count
         *
         * @access public
         * @return int
         */
        public function count()
        {
            return $this->events->count();
        }

    }
