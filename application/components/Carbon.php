<?php

    namespace application\components;

    use \Yii;
    use \CException as Exception;
    use \Carbon\Carbon as CarbonLibrary;
    use \CalendR\Event\EventInterface;

    class Carbon extends CarbonLibrary
    {

        /**
         * Length for Humans
         *
         * @access public
         * @param Carbon $compare
         * @param string $translationCategory
         * @return string
         */
        public function lengthForHumans(Carbon $other, $translationCategory = 'app')
        {
            $isNow = false;
            $isFuture = $this->gt($other);
            $delta = $other->diffInSeconds($this);

            // 4 weeks per month, 365 days per year... good enough!!
            $unit = '{before} {n} year {after}|{n} years {after}';
            $divs = array(
                '{before} {n} second {after}|{n} seconds {after}'           => self::SECONDS_PER_MINUTE,
                '{before} {n} minute {after}|{before} {n} minutes {after}'  => self::MINUTES_PER_HOUR,
                '{before} {n} hour {after}|{before} {n} hours {after}'      => self::HOURS_PER_DAY,
                '{before} {n} day {after}|{before} {n} days {after}'        => self::DAYS_PER_WEEK,
                '{before} {n} week {after}|{before} {n} weeks {after}'      => 4.3,
                '{before} {n} month {after}|{before} {n} months {after}'    => self::MONTHS_PER_YEAR
            );


            foreach ($divs as $divUnit => $divValue) {
                if ($delta < $divValue) {
                    $unit = $divUnit;
                    break;
                }
                $delta = floor($delta / $divValue);
            }

            if ($delta <= 0) {
                $delta = 1;
            }

            return $isFuture
                ? trim(Yii::t($translationCategory, $unit, array($delta, '{before}' => '', '{after}' => 'before')))
                : trim(Yii::t($translationCategory, $unit, array($delta, '{before}' => 'for', '{after}' => '')));
        }

        /**
         * Event Legnth (For Humans)
         *
         * @access public
         * @param EventInterface $event
         * @return string
         */
        public static function eventLengthForHumans(EventInterface $event)
        {
            return self::instance($event->getBegin())
                ->lengthForHumans(self::instance($event->getEnd()));
        }

    }