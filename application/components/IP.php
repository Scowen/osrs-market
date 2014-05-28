<?php

    namespace application\components;

    use \Yii;
    use \CException;

    /**
     * IP Helper
     *
     * IPv6 is a static class that provides a collection of helper methods for dealing with IP addresses. Although it
     * deals with both IPv4 and IPv6, it automatically converts IPv4 addresses into IPv6 so that return values are
     * consistent (for example, being able to save the binary string versions of both IPv4 and IPv6 addresses in the
     * same column in a database).
     *
     * @author      Zander Baldwin <mynameiszanders@gmail.com>
     * @link        https://github.com/mynameiszanders/ip
     * @copyright   2013 Zander Baldwin
     * @license     MIT/X11 <http://j.mp/mit-license>
     */
    class IP extends \CComponent
    {

        /**
         * Protocol to Number
         *
         * Converts a printable IP address into an unpacked binary string.
         * By default, the method always returns a 16-byte string for both IPv4 and IPv6 addresses. By passing
         * bool(true) in the second parameter, you can force a return of a 4-byte string for IPv4 addresses.
         *
         * @static
         * @access public
         * @param string $ip
         * @param boolean $shrink
         * @return string|false
         */
        public static function pton($ip, $shrink = false)
        {
            // Make sure that the IP address given is the correct data-type. If it is not a string then it automatically
            // can't be in either protocol or binary notation.
            if(!is_string($ip)) {
                return false;
            }
            // If the IP address has been given in IPv4 protocol notation, then convert it to a 4-byte binary sequence.
            if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ip = current(unpack('A4', inet_pton($ip)));
            }
            // If the IP address has been given in IPv6 protocol notation, then convert it to a 16-byte binary sequence.
            elseif(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $ip = current(unpack('A16', inet_pton($ip)));
            }
            // Now we have converted any protocol notation addresses to binary sequences, we need to check that the
            // binary sequences are the correct length - just in case an already-binary sequence, or even worse a
            // completely invalid string, was passed.
            if(strlen($ip) === 4 || strlen($ip) === 16) {
                return $shrink
                    ? preg_replace('/^\\0{12}/', '', $ip)
                    : str_pad($ip, 16, "\0", STR_PAD_LEFT);
            }
            // If we couldn't match an IP address in any of the formats, then return false.
            return false;
        }


        /**
         * Number to Protocol
         *
         * Converts an unpacked binary string into a printable IP address.
         *
         * @static
         * @access public
         * @param binary(string) $ip
         * @param boolean $shrink
         * @return string|false
         */
        public static function ntop($ip, $shrink = false)
        {
            // Dealing with IP protocol addresses is really tricky. Convert it to a binary sequence and it's really
            // easy! Run it through the pton() method and if it returns as a string it means the IP is valid and can be
            // converted to a protocol address using the built in PHP functionality. Otherwise return false.
            return is_string($ip = self::pton($ip, $shrink))
                ? inet_ntop(pack('A' . strlen($ip), $ip))
                : false;
        }


        /**
         * Validate IP Address
         *
         * Determines whether the given value is a valid IP address, regardless of it being in binary or protocol
         * format.
         *
         * @static
         * @access public
         * @param string $ip
         * @param integer $type
         * @return boolean
         */
        public static function validate($ip, $type = null)
        {
            // Use the pton() method to determine if the IP addressed passed is valid. If it isn't a string then we
            // can't use it in protocol or binary notation.
            return is_string($binary = self::pton($ip, true))
                && (
                    // Providing that we are not specifically checking for an IPv6, check if the IP address equates to a
                    // 4-byte binary sequence (meaning it could be an IPv4 address in IPv6 notation).
                    ($type !== 6 && strlen($binary) === 4)
                    // Providing that we are not specifically checking for an IPv4 address, check if the IP address is
                    // a 16-byte binary sequence (IPv6) but also check if it could be an IPv4 address in IPv6 notation.
                 || ($type !== 4 && (strlen($binary) === 16 || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)))
                );
        }


        /**
         * Expand IPv6 Protocol
         *
         * Converts an IPv4 or IPv6 protocol address into a full IPv6 address (no double colons).
         *
         * @static
         * @access public
         * @param string $ip
         * @return string
         */
        public static function expand($ip)
        {
            // Attempt to convert the IP address into a full, 16-byte IPv6 binary sequence - but return booL(false) if
            // the IP address turns out to be invalid.
            if(!is_string($ip = self::pton($ip))) {
                return false;
            }
            // Convert the 16-byte binary sequence into a hexadecimal-string representation.
            $hex = unpack('H*hex', $ip);
            // Insert a colon between every block of 4 characters, and return the resulting IP address in full IPv6
            // protocol notation.
            return substr(preg_replace('/([A-f0-9]{4})/', '$1:', $hex['hex']), 0, -1);
        }

        /**
         * Subnet Mask
         *
         * Generates an IPv6 subnet mask for the CIDR value passed.
         *
         * @static
         * @access public
         * @param integer
         * @return string|false
         */
        public static function mask($cidr)
        {
            if(!is_int($cidr) || $cidr < 0 || $cidr > 128) {
                return false;
            }
            // Since it takes 4 bits per hexadecimal, how many sections of complete 1's do we have (f's)?
            $mask = str_repeat('f', floor($cidr / 4));
            // Now we have less than four 1 bits left we need to determine what hexadecimal character should be added
            // next. Of course, we should only add them in there are 1 bits leftover to prevent going over the 128-bit
            // limit.
            if($bits = $cidr % 4) {
                // Create a string representation of a 4-bit binary sequence beginning with the amount of leftover 1's.
                $bin = str_pad(str_repeat('1', $bits), 4, '0', STR_PAD_RIGHT);
                // Convert that 4-bit binary string into a hexadecimal character, and append it to the mask.
                $mask .= dechex(bindec($bin));
            }
            // Fill the rest of the string up with zero's to pad it out to the correct length.
            $mask = str_pad($mask, 32, '0', STR_PAD_RIGHT);
            // Pack the hexadecimal sequence into a real, 16-byte binary sequence.
            $mask = pack('H*', $mask);
            return $mask;
        }


        /**
         * Compare IPs
         *
         * @static
         * @access public
         * @param string $first
         * @param string $second
         * @return boolean
         */
        public static function compare($first, $second)
        {
            return self::pton($first) === self::pton($second);
        }


        /**
         * Network Address
         *
         * @static
         * @access public
         * @param string $ip
         * @param integer $cidr
         * @return string|false
         */
        public static function network($ip, $cidr)
        {
            // Providing that the IP address and CIDR are valid, bitwise AND the IP address binary sequence with the
            // mask generated from the CIDR. Otherwise return bool(false).
            return ($ip = self::pton($ip)) !== false && ($mask = self::mask($cidr)) !== false
                ? $ip & $mask
                : false;
        }


        /**
         * Broadcast Address
         *
         * @static
         * @access public
         * @param string $ip
         * @param integer $cidr
         * @return string|false
         */
        public static function broadcast($ip, $cidr)
        {
            // Providing that the IP address and CIDR are valid, bitwise OR the IP address binary sequence with the
            // inverse of the mask generated from the CIDR. Otherwise return bool(false).
            return ($ip = self::pton($ip)) !== false && ($mask = self::mask($cidr)) !== false
                ? $ip | ~$mask
                : false;
        }


        /**
         * In Range
         *
         * Returns a boolean value depending on whether the IP address in question is within the range of the target
         * IP/CIDR combination.
         *
         * @static
         * @access public
         * @param string $ip
         * @param string $target
         * @param integer $cidr
         * @return boolean
         */
        public static function inRange($ip, $target, $cidr)
        {
            return ($ip     = self::network($ip, $cidr))     !== false
                && ($target = self::network($target, $cidr)) !== false
                ? $ip === $target
                : false;
        }

    }
