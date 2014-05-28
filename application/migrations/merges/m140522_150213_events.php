<?php

    class m140522_150213_events extends BaseMerge
    {

        /**
         * Run Merge
         *
         * @access public
         * @return void
         */
        public function run()
        {
            // Create a query to select every branch from the old database.
            $query = $this->getFromDbConnection()->createCommand()
                ->select()
                ->from('webcal_entry');
            $result = $query->query();

            // Loop over each old branch.
            while($row = $result->read()) {
                $row = (object) $row;

                if($row->cal_create_by == 'x' || ($branch = $this->getRecordId('branches.company', $row->cal_create_by)) === null) {
                    continue;
                }

                $time = strrev(abs($row->cal_time));
                $begin = new DateTime(
                    preg_replace('/^(\\d{4})(\\d{2})(\\d{2})$/',    '\\1-\\2-\\3', trim($row->cal_date))
                  . ' '
                  . (strrev(substr($time, 4, 2)) ?: '00') . ':'
                  . (strrev(substr($time, 2, 2)) ?: '00') . ':'
                  . (strrev(substr($time, 0, 2)) ?: '00')
                );
                $end = clone $begin;
                $end->add(new DateInterval('PT' . ($row->cal_duration > 0 ? $row->cal_duration : 5) . 'M'));

                $this->insert('{{events}}', array(
                    'id' => $row->cal_id,
                    'title' => $row->cal_name,
                    'description' => null,
                    'begin' => $begin->format('U'),
                    'end' => $end->format('U'),
                    'allday' => 0,
                    'creator' => null,
                    'branch' => $branch,
                ));
            }
        }

    }
