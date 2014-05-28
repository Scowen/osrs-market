<?php

    class m140522_104902_users extends BaseMerge
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
                ->from('bigStaff');
            $result = $query->query();

            // Loop over each old branch.
            while($row = $result->read()) {
                $row = (object) $row;

                if($row->branch == 'x' || ($branch = $this->getRecordId('branches.company', $row->branch)) === null) {
                    continue;
                }

                // Only calculate this once per user.
                $password = CPasswordHelper::hashPassword($row->password);

                $i = 1;
                while(true) {
                    try {
                        $this->insert('{{users}}', array(
                            'id'        => (int) $row->egnID,
                            'username'  => strtolower($row->fName) . ($i > 1 ? $i : ''),
                            'email'     => filter_var($email = trim($row->emailAdress), FILTER_VALIDATE_EMAIL)
                                            ? $email
                                            : md5(microtime(true)) . '@' . ($i > 1 ? $i . '.' : '') . 'example.com',
                            'password'  => $password,
                            'branch'    => $branch,
                            'firstname' => ucwords(strtolower(trim($row->fName))) ?: 'Unknown',
                            'nickname'  => null,
                            'lastname'  => ucwords(strtolower(trim($row->lName))) ?: 'Unknown',
                            'created'   => time(),
                            'lastLogin' => null,
                            'active'    => 1,
                            'avatar'    => null,
                            'plaintextToBeDeleted' => $row->password,
                        ));
                    }
                    catch(CDbException $e) {
                        if(!preg_match('/Duplicate.*\'username\'/', $e->getMessage())) {
                            throw $e;
                        }
                        $i++;
                        echo "\r";
                        continue;
                    }
                    break;
                }

            }
        }

    }
