<?php
// require_once 'Zend/Image/Action/ActionAbstract.php';
// require_once 'Zend/Image/Point.php';

class Glitch_Image_Action_DrawEllipse extends Glitch_Image_Action_ActionAbstract {
    /**
     * The point to start the ellipse at
     *
     * @var Glitch_Image_Point _location
     */
    protected $_location = null;

    /**
     * The width of the ellipse
     *
     * @var integer width
     */
    public $_width = null;

    /**
     * The height of the ellipse
     *
     * @var integer height
     */
    public $_height = null;

    /**
     * Determines if the ellipse is filled
     *
     * @var boolean filled
     */
    public $_filled = false;

    /**
     * The width of the stroke
     *
     * @var int stroke width
     */
    protected $_strokeWidth = 1;

    /**
     * The color of the polygon (hex)
     *
     * @var Glitch_Image_Color color
     */
    protected $_strokeColor;

    /**
     *
     *
     * @var array stroke dash
     */
    protected $_strokeDashPattern = array();

    /**
     * The stroke dash offset
     *
     * @var int stroke width
     */
    protected $_strokeDashOffset = 0;

    /**
     * The color of the polygon (hex)
     *
     * @var Glitch_Image_Color color
     */
    protected $_fillColor;

    /**
     * The alpha channel of the stroke
     *
     * @var float alpha
     */
    protected $_strokeAlpha = 100;

    /**
     * The alpha channel of the fill
     *
     * @var float alpha
     */
    protected $_fillAlpha = 100;

    /**
     * The name of this action
     */
    const NAME  = 'DrawEllipse';

    /**
     * Parse the inital options for the ellipse
     *
     * @param  array $options An array with the options of the ellipse
     */
    public function __construct($options=array()) {
        $this->_location = new Glitch_Image_Point();
        
        return parent::__construct($options);
    }
    
    public function addOptions($options = array()) {
        $options = $this->_parseOptions($options);
    
        foreach($options as $key => $value) {
            switch($key) {
                case 'filled':
                    $this->filled($value);
                    break;
                case 'strokeColor':
                    $this->setStrokeColor($value);
                    break;
                case 'fillColor':
                    $this->setFillColor($value);
                    break;
                case 'alpha':
                    $this->setAlpha($value);
                    break;
                case 'startX':
                    $this->setX($value);
                    break;
                case 'startY':
                    $this->setY($value);
                    break;
                case 'location':
                    $this->setLocation($value);
                    break;
                case 'width':
                    $this->setWidth($value);
                    break;
                case 'height':
                    $this->setHeight($value);
                    break;
                default:
                    // require_once 'Zend/Image/Exception.php';
                    throw new Glitch_Image_Exception('Invalid option recognized.');
                    break;
          }
       }

       return $this;
    }

    /**
     * Determine if the ellipse is filled
     *
     * @param  boolean $isFilled (Optional)
     * @return this|_isFilled
     */
    public function filled($isFilled=null) {
        if(null===$isFilled) {
            return $this->_filled;
        }
        $this->_filled = (bool)$isFilled;
        return $this;
    }

    /**
     * Set the width of the ellipse
     *
     * @param int $width the width of the ellipse
     * @return this
     */
    public function setWidth($width) {
        $this->_width = $width;
        return $this;
    }

    /**
     * Set the height of the ellipse
     *
     * @param int $height The height of the ellipse
     * @return this
     */
    public function setHeight($height) {
        $this->_height = $height;
        return $this;
    }

    /**
     * Get the stroke color
     *
     * @return Glitch_Image_Color Stroke color
     */
    public function getStrokeColor() {

        if (null == $this->_strokeColor){
            $this->_strokeColor = new Glitch_Image_Color(0, 0, 0);
        }

        return $this->_strokeColor;
    }

    /**
     * Set stroke color
     * @param Glitch_Image_Color|string $color
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function setStrokeColor($color){

        if (is_string($color)){
            $color = new Glitch_Image_Color($color);
        }

        $this->_strokeColor = $color;
        return $this;
    }

    /**
     * Get the stroke dash
     *
     * @return array Stroke dash
     */
    public function getStrokeDashPattern() {
        return $this->_strokeDashPattern;
    }

    /**
     * Set stroke dash
     * @param array $array
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function setStrokeDashPattern($pattern){
        $this->_strokeDashPattern = $pattern;
        return $this;
    }

    /**
     * Get the stroke dash offset
     *
     * @return string Stroke dash
     */
    public function getStrokeDashOffset() {
        return $this->_strokeDashOffset;
    }

    /**
     * Set stroke dash offset
     * @param string $offset
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function setStrokeDashOffset($offset){
        $this->_strokeDashOffset = $offset;
        return $this;
    }

    /**
     * Set the coordinates of the ellipse
     *
     * @param Glitch_Image_Point|integer $param1 A point or coordinate of the ellipse
     * @param integer $y (Optional)            The Y-coordinate of the ellipse
     * @return this
     */
    public function setLocation($param1,$y = null){
        if($param1 instanceof Glitch_Image_Point) {
            $this->_location = $param1;
        } else {
            $this->_location->setLocation($param1,$y);
        }
        return $this;
    }

    /**
     * Get the location of the ellipse
     *
     * @return location
     */
    public function getLocation() {
        return $this->_location;
    }


    /**
     * Set the starting X-coordinate of the line
     *
     * @param integer $x The X-coordinate to start at
     * @return this
     */
    public function setX($x) {
       $this->_location->setX($x);
       return $this;
    }

    /**
     * Set the starting Y-coordinate of the line
     *
     * @param integer $y The Y-coordinate to start at
     * @return this
     */
    public function setY($y) {
       $this->_location->setY($y);
       return $this;
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
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function setFillColor($color){

        if (is_string($color)){
            $color = new Glitch_Image_Color($color);
        }

        $this->_fillColor = $color;
        return $this;
    }

    /**
     * Get the stroke alpha
     *
     * @return string Stroke color
     */
    public function getStrokeAlpha() {
        return $this->_strokeAlpha;
    }

    /**
     * Set the alpha channel for the stroke
     *
     * @param int $alpha The alpha channel
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function setStrokeAlpha($alpha) {
        $this->_strokeAlpha = $alpha;
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
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function setFillAlpha($alpha) {
        $this->_fillAlpha = $alpha;
        return $this;
    }

    /**
     * Get the stroke width
     *
     * @return int Stroke width
     */
    public function getStrokeWidth() {
        return $this->_strokeWidth;
    }

    /**
     * Set the stroke width
     *
     * @param int $width Stroke width
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function setStrokeWidth($width) {
        $this->_strokeWidth = $width;
        return $this;
    }

    /**
     * Get the width of the ellipse
     *
     * @return integer Width
     */
    public function getWidth() {
        return $this->_width;
    }

    /**
     * Get the height of the ellipse
     *
     * @return integer height
     */
    public function getHeight() {
        return $this->_height;
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
