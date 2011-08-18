<?php
class Glitch_Image_Color {

    /**
     *
     * @var int Red channel
     */
    protected $_red;

    /**
     *
     * @var int Green channel
     */
    protected $_green;

    /**
     *
     * @var int Blue channel
     */
    protected $_blue;

    public function __construct($param1 = 0, $green = null, $blue = null) {
        if($this->_red !== null ||
           $this->_green !== null ||
           $this->_blue !== null)
        {
            throw new Glitch_Image_Exception('Can only set color once.');
        }

        if (strlen($param1) == 6 || $param1[0] == '#'){
            extract(self::calculateRgbFromHex($param1));
        } else {
            $red = $param1;
        }

        $this->_setRGB($red, $green, $blue);
    }

    /**
     *
     * @return array
     */
    public function getRgb($numericKeys = false){
        if($numericKeys === true) {
           return array($this->_red, $this->_green, $this->_blue);
        }

        return array('red' => $this->_red,
                     'green' => $this->_green,
                     'blue' => $this->_blue);
    }

    /**
     *
     * @param int $r
     * @param int $g
     * @param int $bz
     * @return Glitch_Image_Color Fluent interface
     */
    protected function _setRgb($r, $g, $b){
        if(!ctype_digit((string) $r) ||
           !ctype_digit((string) $g) ||
           !ctype_digit((string) $b))
        {
            // require_once 'Zend/Image/Exception.php';
            throw new Glitch_Image_Exception('When setting a color, both
                                Red, Green, and Blue should be an integer');
        }

        /** Clamp values to legal limits. */
        if ($r < 0) { $r = 0; }
        if ($r > 255) { $r = 255; }

        if ($g < 0) { $g = 0; }
        if ($g > 255) { $g = 255; }

        if ($b < 0) { $b = 0; }
        if ($b > 255) { $b = 255; }

        $this->_red = $r;
        $this->_green = $g;
        $this->_blue = $b;
    }

    /**
     *
     * @return string
     */
    public function getHex() {
        $r = str_pad(dechex($this->_red), 2, '0', STR_PAD_LEFT);
        $g = str_pad(dechex($this->_green), 2, '0', STR_PAD_LEFT);
        $b = str_pad(dechex($this->_blue), 2, '0', STR_PAD_LEFT);

        return $r . $g . $b;
    }

    public function __toString() {
        return '#' . $this->getHex();
    }

    /**
     * Calculate the decimal values for each color
     * based on the given hexvalue
     *
     * @param integer $color The color to calculate
     * @return array Decimal values for each color
     */
    public static function calculateRgbFromHex($color){
        if ('#' == $color[0]){
            $color = substr($color, 1);
        }

        $out = array();
        $out['red'] = hexdec(substr($color,0,2));
        $out['green'] = hexdec(substr($color,2,2));
        $out['blue'] = hexdec(substr($color,4,2));

        return $out;
    }
}
