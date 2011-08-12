<?php
// require_once 'Zend/Image/Action//ActionAbstract.php';
// require_once 'Zend/Image/Point.php';

class Glitch_Image_Action_DrawArc extends Glitch_Image_Action_ActionAbstract {
    /*
     * The point to center the arc at
     *
     * @var Glitch_Image_Point _pointCenter
     */
    protected $_pointCenter = null;

    /*
     * Determines if the arc is filled
     *
     * @var boolean filled
     */
    protected $_filled = false;

    /*
     * The width of the arc
     *
     * @var int Width
     */
    protected $_width = 0;

    /*
     * The height of the arc
     *
     * @var int Height
     */
    protected $_height = 0;

    /*
     * The height of the arc
     *
     * @var int Height
     */
    protected $_cutoutStart = 0;


    /*
     * The height of the arc
     *
     * @var int Height
     */
    protected $_cutoutEnd = 0;

    /*
     * The fill color of the arc
     *
     * @var Glitch_Image_Color color
     */
    protected $_fillColor;

    /*
     * The alpha channel of the arc
     *
     * @var int alphachannel
     */
    protected $_fillAlpha = 100;

    /*
     * The name of this action
     */
    const NAME  = 'DrawArc';

    /**
     * Parse the inital options for the line
     *
     * @param  array $options An array with the options of the line
     */
    public function __construct($options=array()) {
        $this->_pointCenter = new Glitch_Image_Point();

        return parent::__construct($options);
    }
    
    public function addOptions($options = array()) {
        $options = $this->_parseOptions($options);
        
        foreach($options as $key => $value) {
            switch($key) {
                case 'filled':
                    $this->filled($value);
                    break;
                case 'pointCenter':
                    $this->setLocation($value);
                    break;
                case 'centerX':
                    $this->setCenterX($value);
                    break;
                case 'centerY':
                    $this->setCenterY($value);
                    break;
                case 'width':
                    $this->setWidth($value);
                    break;
                case 'height':
                    $this->setHeight($value);
                    break;
                case 'cutoutStart':
                    $this->setCutoutStart($value);
                    break;
                case 'cutoutEnd':
                    $this->setCutoutEnd($value);
                    break;
                case 'fillColor':
                    $this->setFillColor($value);
                    break;
                case 'centerPoint':
                    $this->setCenterPoint($value);
                    break;
                default:
                     // require_once 'Zend/Image/Exception.php';
                     throw new Glitch_Image_Exception("Unknown option given: $key");
                     break;
            }
        }
        
        return $this;
    }

    /**
     * Determine if the arc is filled
     *
     * @return bool
     */
    public function isFilled() {
        return $this->_filled;
    }

    /**
     * Set whether the arc is filled
     *
     * @param  boolean $isFilled
     * @return Glitch_Image_Action_Arc Provides fluent interface
     */
    public function setFilled($fill = false) {
        $this->_filled = (bool)$fill;
        return $this;
    }

    /**
     * Set the center location of the arc
     *
     * @param Glitch_Image_Point|integer $param1 A point or coordinate to center the arc at
     * @param integer $y (Optional)            The Y-coordinate to center the arc at
     * @return this
     */
    public function setLocation($param1,$y = null){
        if($param1 instanceof Glitch_Image_Point) {
            $this->_pointCenter = $param1;
        } else {
            $this->_pointCenter->setLocation($param1,$y);
        }
        return $this;
    }

    /**
     * Set the X-coordinate of the center of the arc
     *
     * @param integer $x The X-coordinate to center the arc at
     * @return this
     */
    public function setCenterX($x) {
       $this->_pointCenter->setX($x);
       return $this;
    }

    /**
     * Set the Y-coordinate of the center of the arc
     *
     * @param integer $y The Y-coordinate to center the arc at
     * @return this
     */
    public function setCenterY($y) {
       $this->_pointCenter->setY($y);
       return $this;
    }

    /**
     * Get the fill alpha
     *
     * @return string Fill alpha
     */
    public function getFillAlpha() {
        return $this->_fillAlpha;
    }

    /**
     * Set the alpha channel for the fill
     *
     * @param int $alpha The alpha channel
     * @return Glitch_Image_Action_Arc Provides fluent interface
     */
    public function setFillAlpha($alpha) {
        $this->_fillAlpha = $alpha;
        return $this;
    }

    /**
     * Get the location of the center of the arc
     *
     * @return Glitch_Image_Point
     */
    public function getLocation(Glitch_Image_Adapter_AdapterAbstract $adapter=null) {
        if($adapter!==null) {
            if($this->_pointCenter->getX()===null) {
                $this->_pointCenter->setX($adapter->getWidth()/2);
            }

            if($this->_pointCenter->getY()===null) {
                $this->_pointCenter->setY($adapter->getHeight()/2);
            }
        }

        return $this->_pointCenter;
    }

    /**
     * Get the fill color
     *
     * @return Glitch_Image_Color Fill color
     */
    public function getFillColor() {
        if (null == $this->_fillColor){
            $this->setFillColor(new Glitch_Image_Color(0, 0, 0));
        }

        return $this->_fillColor;
    }

    /**
     *
     * @param Glitch_Image_Color|string $color
     * @return Glitch_Image_Action_DrawArc Provides fluent interface
     */
    public function setFillColor($color){

        if (is_string($color)){
            $color = new Glitch_Image_Color($color);
        }

        $this->_fillColor = $color;
        return $this;
    }

    /**
     * Set the width of the arc
     *
     * @param int $width The width of the arc
     * @return this
     */
    public function setWidth($width) {
        $this->_width = $width;
        return $this;
    }

    /**
     * Get width of the arc
     *
     * @return int Width of the arc
     */
    public function getWidth() {
        return $this->_width;
    }

    /**
     * Set the height of the arc
     *
     * @param int $height The height of the arc
     * @return this
     */
    public function setheight($height) {
        $this->_height = $height;
        return $this;
    }

    /**
     * Get height of the arc
     *
     * @return int height of the arc
     */
    public function getheight() {
        return $this->_height;
    }

    /**
     * Set the cutoutStart of the arc
     *
     * @param int $cutoutStart The cutoutStart of the arc
     * @return this
     */
    public function setcutoutStart($cutoutStart) {
        $this->_cutoutStart = $cutoutStart;
        return $this;
    }

    /**
     * Get cutoutStart of the arc
     *
     * @return int cutoutStart of the arc
     */
    public function getcutoutStart() {
        return $this->_cutoutStart;
    }

    /**
     * Set the cutoutEnd of the arc
     *
     * @param int $cutoutEnd The cutoutEnd of the arc
     * @return this
     */
    public function setcutoutEnd($cutoutEnd) {
        $this->_cutoutEnd = $cutoutEnd;
        return $this;
    }

    /**
     * Get cutoutEnd of the arc
     *
     * @return int cutoutEnd of the arc
     */
    public function getcutoutEnd() {
        return $this->_cutoutEnd;
    }

    /**
     * Get the center of the arc
     *
     * @return int centerpoint of the arc
     */
    public function getCenterPoint() {
        return $this->_pointCenter;
    }


    /**
     * Set the center of the arc
     *
     * @param int $centerPoint The center of the arc
     * @return Glitch_Image_Action_DrawArc
     */
    public function setCenterPoint($centerPoint) {
        $this->_pointCenter = $centerPoint;
        return $this;
    }

    /**
     * Get the name of this action
     *
     * @return self::NAME
     */
    public function getName() {
        return self::NAME;
    }
}
