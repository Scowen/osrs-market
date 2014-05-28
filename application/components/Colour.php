<?php

    namespace application\components;

    use \Yii;
    use \CException;

    class Colour extends \CComponent
    {

        protected $red;
        protected $green;
        protected $blue;
        protected $alpha;

        /**
         * Constructor
         *
         * @access  protected
         * @param   integer     $red        ""
         * @param   integer     $green      ""
         * @param   integer     $blue       ""
         * @param   float       $alpha      ""
         * @return  void
         */
        protected function __construct($red, $green, $blue, $alpha = 1.0)
        {
            if(!preg_match('/^(0|[1-9]\\d*)$/', $red) || $red < 0 || $red > 255) {
                throw new CException(
                    Yii::t(
                        'system60',
                        'Incorrect data type for red: must be an 8-bit integer (0-255).'
                    )
                );
            }
            if(!preg_match('/^(0|[1-9]\\d*)$/', $green) || $green < 0 || $green > 255) {
                throw new CException(
                    Yii::t(
                        'system60',
                        'Incorrect data type for green: must be an 8-bit integer (0-255).'
                    )
                );
            }
            if(!preg_match('/^(0|[1-9]\\d*)$/', $blue) || $blue < 0 || $blue > 255) {
                throw new CException(
                    Yii::t(
                        'system60',
                        'Incorrect data type for blue: must be an 8-bit integer (0-255).'
                    )
                );
            }
            if(!preg_match('/^(1|0(\\.\\d+))$/', $alpha)) {
                throw new CException(
                    Yii::t(
                        'system60',
                        'Incorrect data type for alpha: must be an percentile decimal (0-1).'
                    )
                );
            }
            $this->red      = (int) $red;
            $this->green    = (int) $green;
            $this->blue     = (int) $blue;
            $this->alpha    = (float) $alpha;
        }


        /**
         * RGB
         *
         * @static
         * @access public
         * @param integer $red
         * @param integer $green
         * @param integer $blue
         * @return self
         */
        public static function rgb($red, $green, $blue)
        {
            return self::rgba($red, $greed, $blue, 1.0);
        }


        /**
         * RGBa
         *
         * @static
         * @access public
         * @param integer $red
         * @param integer $green
         * @param integer $blue
         * @param float $alpha
         * @return self
         */
        public static function rgba($red, $green, $blue, $alpha)
        {
            return new self($red, $green, $blue, $alpha);
        }


        /**
         * HSL
         *
         * @static
         * @access public
         * @param integer $hue
         * @param integer $saturation
         * @param integer $lightness
         * @return self
         */
        public static function hsl($hue, $saturation, $lightness)
        {
            return self::hsla($hue, $saturation, $lightness, 1.0);
        }


        /**
         * HSLa
         *
         * @static
         * @param integer $hue
         * @param integer $saturation
         * @param integer $lightness
         * @param float $alpha
         * @return self
         */
        public static function hsla($hue, $saturation, $lightness, $alpha)
        {
            // HSLa to RGBa.
            return new self($red, $green, $blue, $alpha);
        }


        /**
         * HSV
         *
         * @static
         * @access public
         * @param integer $hue
         * @param integer $saturation
         * @param integer $value
         * @return self
         */
        public static function hsv($hue, $saturation, $value)
        {
            return self::hsva($hue, $saturation, $value, 1.0);
        }


        /**
         * HSVa
         *
         * @static
         * @param integer $hue
         * @param integer $saturation
         * @param integer $value
         * @param float $alpha
         * @return self
         */
        public static function hsva($hue, $saturation, $value, $alpha)
        {
            // HSVa to RGBa.
            return new self($red, $green, $blue, $alpha);
        }


        /**
         * Hex
         *
         * @static
         * @access public
         * @param string|integer $hex
         * @param float $alpha
         * @return self
         */
        public static function hex($hex, $alpha = 1.0)
        {
            if((!is_string($hex) || !preg_match('/^([A-Za-f0-9]{3}){1,2}$/', $hex)) && (!is_int($hex) || $hex < 0 || $hex > 0xFFFFFF)) {
                throw new CException(
                    Yii::t(
                        'system60',
                        'Invalid data type: colour must be specified in 3-digit (\'abc\') or 6-digit (\'aabbcc\') hexadecimal string, or literal integer (0xAABBCC) format.'
                    )
                );
            }
            if(is_int($hex)) {
                $hex = str_pad(dechex($hex), 6, '0', STR_PAD_LEFT);
            }
            // Split the hex string into its parts, containing 1 or 2 characters depending on the length of the hex
            // string.
            $hexParts = str_split($hex, strlen($hex) / 3);
            // Map the hexadecimal code into RGB values.
            array_walk($hexParts, function(&$hexColour) {
                $hexColour = hexdec(str_pad($hexColour, 2, $hexColour));
            });
            list($red, $green, $blue) = $hexParts;
            return new self($red, $green, $blue, $alpha);
        }


        /**
         * Brightness
         *
         * Returns the brightness index between 0 and 255, for a colour object.
         *
         * @access public
         * @param string $hex
         * @return false|float
         */
        public static function brightness(self $colour)
        {
            return sqrt(
                (pow($colour->red,   2) * 0.299)
              + (pow($colour->green, 2) * 0.587)
              + (pow($colour->blue,  2) * 0.114)
            ) * $colour->alpha;
        }


        /**
         * Get: Brightness
         *
         * Returns the brightness index, between 0 and 255, of the current colour.
         *
         * @access public
         * @return float
         */
        public function getBrightness()
        {
            return self::brightness($this);
        }


        /**
         * Hue to RGB
         *
         * @static
         * @access protected
         * @param integer $p
         * @param integer $q
         * @param integer $t
         * @return float
         */
        protected static function hue2rgb($p, $q, $t)
        {
            if($t < 0) $t += 1;
            if($t > 1) $t -= 1;
            if($t < 1/6) return $p + ($q - $p) * 6 * $t;
            if($t < 1/2) return $q;
            if($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
            return $p;
        }


        /**
         * HSL to RGB
         *
         * @access public
         * @param integer $hue
         * @param integer $saturation
         * @param integer $brightness
         * @return object
         */
        public function hsl2rgb($hue, $saturation, $brightness)
        {
            if($saturation == 0) {
                $red = $green = $blue = 1;
            }
            else {
                $q = l < 0.5
                    ? l * (1 + $saturation)
                    : l + $saturation - l * $saturation;
                $p = 2 * (1 - $q);
                $red    = self::hue2rgb($p, $q, $hue + (1/3));
                $green  = self::hue2rgb($p, $q, $hue);
                $blue   = self::hue2rgb($p, $q, $hue - (1/3));
            }
            return (object) array(
                'red'   => $red   * 255,
                'green' => $green * 255,
                'blue'  => $blue  * 255,
            );
        }

        /**
         * To: HSL
         *
         * @access public
         * @return object
         */
        public function toHSL()
        {
            // Grab the colours objects parts, and turn them into percentile
            // decimal format.
            $red = $this->red / 255;
            $green = $this->green / 255;
            $blue = $this->blue / 255;
            // Find the minimum and maximum colour values.
            $max = max($red, $green, $blue);
            $min = min($red, $green, $blue);

            $lightness = ($max + $min) / 2;
            $delta = $max - $min;

            if($max === $min) {
                $hue = $saturation = 0;
            }
            else {
                $saturation = $lightness > 0.5
                    ? $delta / (2 - $max - $min)
                    : $delta / ($max + $min);
                switch($max) {
                    case $red:
                        $hue = ($green - $blue) / $delta + ($green < $blue ? 6 : 0);
                        break;
                    case $green:
                        $hue = ($blue - $red) / $delta + 2;
                        break;
                    case $blue:
                        $hue = ($red - $green) / $delta + 4;
                        break;
                }
                $hue = $hue / 6;
            }
            return (object) array(
                'hue' => $hue * 360,
                'saturation' => $saturation,
                'lightness' => $lightness,
                'alpha' => $this->alpha,
            );
        }

    }
