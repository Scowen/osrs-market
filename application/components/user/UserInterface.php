<?php

    namespace application\components\user;

    interface UserInterface
    {

        /**
         * Verify Password
         *
         * This method returns a boolean value indicating whether there was a match between the password specified as a
         * parameter to this method, and the user's actual persisted password (in a database, memory, config file, etc).
         *
         * @access public
         * @param string $password
         * @return boolean
         */
        public function verifyPassword($password);

        /**
         * IP Allowed?
         *
         * Is the user allowed to log in from the specified IP address? This method accepts an IP address in either
         * notation and binary format (both IPv4 and IPv6), and returns a boolean value.
         *
         * @access public
         * @param string $ip
         * @return boolean
         */
        public function ipAllowed($ip);

    }
