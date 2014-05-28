<?php

    class m140521_125848_branches extends BaseMerge
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
                ->from('CentreDetails');
            $result = $query->query();

            // Loop over each old branch.
            while($row = $result->read()) {
                $row = (object) $row;
                // If the branch does not have a centre it belongs to, skip it.
                if($row->Centre == 'x') {
                    continue;
                }

                $postcode = '/^(GIR\\s?0AA)|((([A-Z-[QVX]]\\d\\d?)|(([A-Z-[QVX]][A-Z-[IJZ]]\\d\\d?)|(([A-Z-[QVX]]\\d[A-HJKSTUW])|([A-Z-[QVX]][A-Z-[IJZ]]\\d[ABEHMNPRVWXY]))))\\s?\\d[A-Z-[CIKMOV]]{2})$/';
                // Create a new Branch model, assign the correct attributes, then save it to the new database.
                $this->insert('{{branches}}', array(
                    'id' => (int) $row->egnID,
                    'name'      => $row->CentreName,
                    'company'   => $row->Centre,
                    'address'   => $row->Address,
                    'postcode'  => preg_match($postcode, $row->PostCode)
                                    ? preg_replace('/\\s+/', '', $row->PostCode)
                                    : null,
                    'sales'     => preg_replace('/[^\\d]/', '', $row->SalesNo)      ?: null,
                    'internal'  => preg_replace('/[^\\d]/', '', $row->InternalNo)   ?: null,
                    'fax'       => preg_replace('/[^\\d]/', '', $row->FaxNo)        ?: null,
                    'email'     => $row->Email ?: null,
                    'vat'       => $row->VatNo ?: null,
                    'reg'       => null,
                    'active'    => $row->active ? 1 : 0,
                ));
            }
        }

    }
