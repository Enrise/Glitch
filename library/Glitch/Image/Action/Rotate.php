<?php
// require_once 'Zend/Image/Action/ActionAbstract.php';
// require_once 'Zend/Image/Color.php';

/**
 * Crop prototype
 * Should support definition of anything known to form a crop, i.e.
 * top left corner (crop to image edge)   DONE
 * top left corner + w + h (crop box)     DONE
 * bottom right corner + w + h (crop box)
 * calulation of coords from subsequent calls to setWidth/setHeight
 */

class Glitch_Image_Action_Rotate extends Glitch_Image_Action_ActionAbstract
{

    const NAME  = 'Rotate';

    /**
     *
     * @var Glitch_Image_Color
     */
    protected $_backgroundColor;

    /**
     *
     * @var float
     */
    protected $_angle;

    public function addOptions($options = array()) {
        $options = $this->_parseOptions($options);

        foreach($options as $key => $value) {

            switch($key) {
                case 'background':
                case 'backgroundColor':
                    $this->setBackgroundColor($value);
                    break;
                case 'angle':
                    $this->setAngle($value);
                    break;
            }
        }

        return $this;
    }

    /**
     * Get the background color
     *
     * @return string background color
     */
    public function getBackgroundColor($asString = false) {
        if($this->_backgroundColor === null) {
            $this->setBackgroundColor(0, 0, 0);
        }
    
        if($asString) {
            return $this->_backgroundColor->__toString();
        }
    
        return $this->_backgroundColor;
    }

    /**
     * Set background
     * @param string $background
     * @return Glitch_Image_Action_Rotate Provides fluent interface
     */
    public function setBackgroundColor($color, $g = null, $b = null) {
        $this->_backgroundColor = new Glitch_Image_Color($color, $g, $b);

        return $this;
    }

    /**
     *
     *
     * @return int angle
     */
    public function getAngle() {
        return $this->_angle;
    }

    /**
     *
     *
     * @param  int $angle
     * @return Glitch_Image_Action_Rotate Provides fluent interface
     */
    public function setAngle($angle){
        $this->_angle = (int) $angle;

        return $this;
    }

    /**
     * Get the name of this action
     *
     * @return string Action name
     */
    public function getName() {
        return self::NAME;
    }
}
