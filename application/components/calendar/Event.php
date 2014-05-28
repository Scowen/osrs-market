<?php

    namespace application\components\calendar;

    use \Yii;
    use \CException as Exception;
    use \application\components\ActiveRecord;
    use \CalendR\Event\EventInterface;
    use \CalendR\Period\PeriodInterface;

    abstract class Event extends ActiveRecord implements EventInterface
    {

        protected $beginObject;
        protected $endObject;

        /**
         * Get: Begin Object
         *
         * @access public
         * @return DateTime
         */
        public function getBeginObject()
        {
            if(isset($this->beginObject)) {
                return clone $this->beginObject;
            }
            try {
                $this->beginObject = new \DateTime('@' . $this->begin);
                return clone $this->beginObject;
            }
            catch(\Exception $e) {
                return null;
            }
        }

        /**
         * Get: Begin
         *
         * Because of the way the ActiveRecord model works, this function will not work. We are only including it
         * because it is required by the Event Interface.
         *
         * @access public
         * @return DateTime
         */
        public function getBegin()
        {
            return $this->getBeginObject();
        }

        /**
         * Get: End Object
         *
         * @access public
         * @return DateTime
         */
        public function getEndObject()
        {
            if(isset($this->endObject)) {
                return clone $this->endObject;
            }
            try {
                $this->endObject = new \DateTime('@' . $this->end);
                return clone $this->endObject;
            }
            catch(\Exception $e) {
                return null;
            }
        }

        /**
         * Get: End
         *
         * Because of the way the ActiveRecord model works, this function will not work. We are only including it
         * because it is required by the Event Interface.
         *
         * @access public
         * @return DateTime
         */
        public function getEnd()
        {
            return $this->getEndObject();
        }

        /**
         * Contains
         *
         * Check if the given date is during the event.
         *
         * @access public
         * @param  DateTime $datetime
         * @return boolean
         */
        public function contains(\DateTime $datetime)
        {
            return $this->getBegin()->diff($datetime)->invert == 0 && $this->getEnd()->diff($datetime)->invert == 1;
        }

        /**
         * Contains Period
         *
         * Check if the given period is during the event
         *
         * @access public
         * @param  CalendR\Period\PeriodInterface $period
         * @return boolean
         */
        public function containsPeriod(PeriodInterface $period)
        {
            return $this->getBegin()->diff($period->getBegin())->invert == 0
                && $this->getEnd()->diff($period->getEnd())->invert == 1;
        }

        /**
         * Is During?
         *
         * Check if the event is during the given period
         *
         * @access public
         * @param  CalendR\Period\PeriodInterface $period
         * @return boolean
         */
        public function isDuring(PeriodInterface $period)
        {
            return $this->getBeginObject() >= $period->getBegin() && $this->getEndObject() < $period->getEnd();
        }

    }
