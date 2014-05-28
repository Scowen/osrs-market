<?php

    use \application\components\IP;

    class IPComponentTest extends CTestCase
    {

        /**
         * Test: Protocol to Number (Protocol Inputs)
         *
         * @test
         * @access public
         * @return void
         */
        public function testPtonProtocol()
        {
            $IPv4 = '12.34.56.78';
            $IPv46 = '::12.34.56.78';
            $IPv6 = '2001:db8::a60:8a2e:370:7334';
            // Assert that the correct values are returned when a valid IPv4 protocol address is used.
            $this->assertSame(16, strlen(IP::pton($IPv4)));
            $this->assertSame(pack('H*', '0000000000000000000000000c22384e'), IP::pton($IPv4));
            $this->assertSame(4,strlen(IP::pton($IPv4, true)));
            $this->assertSame(pack('H*', '0c22384e'), IP::pton($IPv4, true));
            // Assert that the correct values are returned when a valid IPv6 protocol address is used.
            $this->assertSame(16, strlen(IP::pton($IPv6)));
            $this->assertSame(pack('H*', '20010db8000000000a608a2e03707334'), IP::pton($IPv6));
            $this->assertSame(16, strlen(IP::pton($IPv6, true)));
            $this->assertsame(pack('H*', '20010db8000000000a608a2e03707334'), IP::pton($IPv6, true));
            // Assert that the correct values are returned when a valid IPv4 address in IPv6 protocol notation is used.
            $this->assertSame(16, strlen(IP::pton($IPv46)));
            $this->assertSame(pack('H*', '0000000000000000000000000c22384e'), IP::pton($IPv46));
            $this->assertSame(4, strlen(IP::pton($IPv46, true)));
            $this->assertSame(pack('H*', '0c22384e'), IP::pton($IPv46, true));
            // Define IP addresses with out-of-range numbers.
            $IPv4 = '12.34.567.89';
            $IPv6 = '2001:db8::a60:8a2e:370g:7334';
            // Assert that the method returns false when an invalid IPv4 protocol address is used.
            $this->assertSame(false, IP::pton($IPv4));
            $this->assertSame(false, IP::pton($IPv4, true));
            // Assert that the method returns false when an invalid IPv6 protocol address is used.
            $this->assertSame(false, IP::pton($IPv6));
            $this->assertSame(false, IP::pton($IPv6, true));
            // Assert that the method returns false when an invalid data-type is passed.
            $this->assertSame(false, IP::pton(123));
            $this->assertSame(false, IP::pton(1.3));
            $this->assertSame(false, IP::pton(array()));
            $this->assertSame(false, IP::pton((object) array()));
            $this->assertSame(false, IP::pton(null));
        }

        /**
         * Test: Protocol to Number (Binary Inputs)
         *
         * @test
         * @access public
         * @return void
         */
        public function testPtonBinary()
        {
            $IPv4short  = pack('H*', '0c22384e');
            $IPv4long   = pack('H*', '0000000000000000000000000c22384e');
            $IPv6       = pack('H*', '20010db8000000000a608a2e03707334');
            // Assert that the correct values are returned when a valid IPv4 protocol address is used.
            $this->assertSame(16, strlen(IP::pton($IPv4short)));
            $this->assertSame($IPv4long, IP::pton($IPv4short));
            $this->assertSame(4, strlen(IP::pton($IPv4short, true)));
            $this->assertSame($IPv4short, IP::pton($IPv4short, true));
            // Assert that the correct values are returned when a valid IPv4 address in IPv6 notation is used.
            $this->assertSame(16, strlen(IP::pton($IPv4long)));
            $this->assertSame($IPv4long, IP::pton($IPv4long));
            $this->assertSame(4, strlen(IP::pton($IPv4long, true)));
            $this->assertSame($IPv4short, IP::pton($IPv4long, true));
        }

        /**
         * Test: Number to Protocol (Protocol Inputs)
         *
         * @test
         * @access public
         * @return void
         */
        public function testNtopProtocol()
        {

            $IPv4short  = '12.34.56.78';
            $IPv4long   = '::12.34.56.78';
            $IPv6short  = '2001:db8::a60:8a2e:370:7334';
            $IPv6long   = '2001:0db8:0000:0000:0a60:8a2e:0370:7334';
            // Assert that the same strings get returned when valid short notation is used.
            $this->assertSame($IPv4long, IP::ntop($IPv4short));
            $this->assertSame($IPv4long, IP::ntop($IPv4long));
            $this->assertSame($IPv6short, IP::ntop($IPv6short));
            $this->assertSame($IPv6short, IP::ntop($IPv6long));
            $this->assertSame($IPv4short, IP::ntop($IPv4short, true));
            $this->assertSame($IPv4short, IP::ntop($IPv4long, true));
            $this->assertSame($IPv6short, IP::ntop($IPv6short, true));
            $this->assertSame($IPv6short, IP::ntop($IPv6long, true));
            // Define IP addresses with out-of-range numbers.
            $IPv4 = '12.34.567.89';
            $IPv6 = '2001:db8::a60:8a2e:370g:7334';
            // Assert that bool(false) gets returned on invalid IP addresses get passed.
            $this->assertSame(false, IP::ntop($IPv4));
            $this->assertSame(false, IP::ntop($IPv6));
            $this->assertSame(false, IP::ntop($IPv4, true));
            $this->assertSame(false, IP::ntop($IPv6, true));
            // Assert that the method returns false when an invalid data-type is passed.
            $this->assertSame(false, IP::ntop(123));
            $this->assertSame(false, IP::ntop(1.3));
            $this->assertSame(false, IP::ntop(array()));
            $this->assertSame(false, IP::ntop((object) array()));
            $this->assertSame(false, IP::ntop(null));
        }

        /**
         * Test: Number to Protocol (Binary Inputs)
         *
         * @test
         * @access public
         * @return void
         */
        public function testNtopBinary()
        {

            $IPv4short  = pack('H*', '0c22384e');
            $IPv4long   = pack('H*', '0000000000000000000000000c22384e');
            $IPv6long   = pack('H*', '20010db8000000000a608a2e03707334');
            $IPv4  = '12.34.56.78';
            $IPv4_6   = '::12.34.56.78';
            $IPv6  = '2001:db8::a60:8a2e:370:7334';
            $this->assertSame($IPv4_6, IP::ntop($IPv4short));
            $this->assertSame($IPv4_6, IP::ntop($IPv4long));
            $this->assertSame($IPv6, IP::ntop($IPv6long));
            $this->assertSame($IPv4, IP::ntop($IPv4short, true));
            $this->assertSame($IPv4, IP::ntop($IPv4long, true));
            $this->assertSame($IPv6, IP::ntop($IPv6long, true));
        }

        /**
         * Test: Validate IP
         *
         * @test
         * @access public
         * @return void
         */
        public function testValidate()
        {
            // Any IP address.
            $this->assertSame(true, IP::validate('12.34.56.78'));
            $this->assertSame(true, IP::validate('::12.34.56.78'));
            $this->assertSame(true, IP::validate('2001:db8::a60:8a2e:370:7334'));
            $this->assertSame(true, IP::validate('2001:0db8:0000:0000:0a60:8a2e:0370:7334'));
            $this->assertSame(true, IP::validate('::1'));
            $this->assertSame(false, IP::validate('12.34.567.89'));
            $this->assertSame(false, IP::validate('2001:db8::a60:8a2e:370g:7334'));
            $this->assertSame(false, IP::validate('1.2.3'));
            $this->assertSame(false, IP::validate(true));
            $this->assertSame(false, IP::validate(array()));
            $this->assertSame(false, IP::validate((object) array()));
            $this->assertSame(false, IP::validate(123));
            $this->assertSame(false, IP::validate(12.3));
            $this->assertSame(false, IP::validate(null));
            // Specifically IPv4 Addresses.
            $this->assertSame(true, IP::validate('12.34.56.78', 4));
            $this->assertSame(true, IP::validate('::12.34.56.78', 4));
            $this->assertSame(false, IP::validate('2001:db8::a60:8a2e:370:7334', 4));
            $this->assertSame(false, IP::validate('2001:0db8:0000:0000:0a60:8a2e:0370:7334', 4));
            $this->assertSame(true, IP::validate('::1', 4));
            $this->assertSame(false, IP::validate('12.34.567.89', 4));
            $this->assertSame(false, IP::validate('2001:db8::a60:8a2e:370g:7334', 4));
            $this->assertSame(false, IP::validate('1.2.3', 4));
            $this->assertSame(false, IP::validate(true, 4));
            $this->assertSame(false, IP::validate(array(), 4));
            $this->assertSame(false, IP::validate((object) array(), 4));
            $this->assertSame(false, IP::validate(123, 4));
            $this->assertSame(false, IP::validate(12.3, 4));
            $this->assertSame(false, IP::validate(null, 4));
            // Specifically IPv6 Addresses.
            $this->assertSame(false, IP::validate('12.34.56.78', 6));
            $this->assertSame(true, IP::validate('::12.34.56.78', 6));
            $this->assertSame(true, IP::validate('2001:db8::a60:8a2e:370:7334', 6));
            $this->assertSame(true, IP::validate('2001:0db8:0000:0000:0a60:8a2e:0370:7334', 6));
            $this->assertSame(true, IP::validate('::1', 6));
            $this->assertSame(false, IP::validate('12.34.567.89', 6));
            $this->assertSame(false, IP::validate('2001:db8::a60:8a2e:370g:7334', 6));
            $this->assertSame(false, IP::validate('1.2.3', 6));
            $this->assertSame(false, IP::validate(true, 6));
            $this->assertSame(false, IP::validate(array(), 6));
            $this->assertSame(false, IP::validate((object) array(), 6));
            $this->assertSame(false, IP::validate(123, 6));
            $this->assertSame(false, IP::validate(12.3, 6));
            $this->assertSame(false, IP::validate(null, 6));
        }

        /**
         * Test: Expand IP
         *
         * @test
         * @access public
         * @return void
         */
        public function testExpand()
        {
            $this->assertSame('0000:0000:0000:0000:0000:0000:0c22:384e', IP::expand('12.34.56.78'));
            $this->assertSame('0000:0000:0000:0000:0000:0000:0c22:384e', IP::expand('::12.34.56.78'));
            $this->assertSame('2001:0db8:0000:0000:0a60:8a2e:0370:7334', IP::expand('2001:db8::a60:8a2e:370:7334'));
            $this->assertSame('2001:0db8:0000:0000:0a60:8a2e:0370:7334', IP::expand('2001:0db8:0000:0000:0a60:8a2e:0370:7334'));
            $this->assertSame(false, IP::expand('12.34.567.89'));
            $this->assertSame(false, IP::expand('2001:db8::a60:8a2e:370g:7334'));
            $this->assertSame(false, IP::expand(123));
            $this->assertSame(false, IP::expand(12.3));
            $this->assertSame(false, IP::expand(true));
            $this->assertSame(false, IP::expand(null));
            $this->assertSame(false, IP::expand(array()));
            $this->assertSame(false, IP::expand((object) array()));
        }

        /**
         * Test: CIDR Mask
         *
         * @test
         * @access public
         * @return void
         */
        public function testMask()
        {
            $this->assertSame(pack('H*', '00000000000000000000000000000000'), IP::mask(0));
            $this->assertSame(pack('H*', 'ffffffffffffffffffffffffffffffff'), IP::mask(128));
            $this->assertSame(pack('H*', 'ffffffffffffffff0000000000000000'), IP::mask(64));
            $this->assertSame(pack('H*', '80000000000000000000000000000000'), IP::mask(1));
            $this->assertSame(pack('H*', 'c0000000000000000000000000000000'), IP::mask(2));
            $this->assertSame(pack('H*', 'e0000000000000000000000000000000'), IP::mask(3));
            $this->assertSame(pack('H*', 'f0000000000000000000000000000000'), IP::mask(4));
            $this->assertSame(pack('H*', 'f8000000000000000000000000000000'), IP::mask(5));
            $this->assertSame(false, IP::mask(-1));
            $this->assertSame(false, IP::mask(129));
            $this->assertSame(false, IP::mask('0'));
            $this->assertSame(false, IP::mask('128'));
            $this->assertSame(false, IP::mask(12.3));
            $this->assertSame(false, IP::mask(true));
            $this->assertSame(false, IP::mask(null));
            $this->assertSame(false, IP::mask(array()));
            $this->assertSame(false, IP::mask((object) array()));
            $this->assertSame(false, IP::mask(array()));
        }

    }
