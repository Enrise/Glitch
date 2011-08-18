<?php
// require_once 'Zend/Image/Action/ActionAbstract.php';
// require_once 'Zend/Image/Point.php';
// require_once 'Zend/Image/Color.php';

class Glitch_Image_Action_DrawPolygon extends Glitch_Image_Action_ActionAbstract
{

    const NAME  = 'DrawPolygon';

    /**
     * Determines if the polygon is filled
     *
     * @var boolean filled
     */
    protected $_filled = false;

    /**
     * Determines if the polygon is closed
     *
     * @var boolean closed
     */
    protected $_closed = true;

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
     * The points to which the polygon
     * needs to be drawn
     *
     * @var array points
     */
    protected $_points = array();

    /**
     * Parse the inital options for the polygon
     *
     * @param mixed $options An array with the options of the polygon
     */
    public function addOptions($options = array()) {
        $options = $this->_parseOptions($options);
    
        foreach($options as $key => $value) {

            switch($key) {
                case 'strokeWidth':
                    $this->setStrokeWidth($value);
                    break;
                case 'strokeDashArray':
                    $this->setStrokeDashArray($value);
                    break;
                case 'strokeDashOffset':
                    $this->setStrokeDashOffset($value);
                    break;
                case 'points':
                    $this->addPoints($value);
                    break;
                case 'filled':
                    $this->setFilled($value);
                    break;
                case 'close':
                    $this->close($value);
                    break;
                case 'strokeColor':
                    $this->setStrokeColor($value);
                    break;
                case 'fillColor':
                    $this->setFillColor($value);
                    break;
                case 'strokeAlpha':
                    $this->setStrokeAlpha($value);
                    break;
                case 'fillAlpha':
                    $this->setFillAlpha($value);
                    break;
            }
        }
        
        return $this;
    }

    /**
     * Determine if the polygon is filled
     *
     * @return bool
     */
    public function isFilled() {
        return $this->_filled;
    }

    /**
     * Determine if the polygon is filled
     *
     * @param  boolean $isFilled
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function setFilled($fill = false) {
        $this->_filled = (bool)$fill;
        return $this;
    }

    /**
     * Determine if the polygon is filled
     *
     * @return bool
     */
    public function isClosed() {
        return $this->_closed;
    }

    /**
     * Determine if the polygon is closed
     *
     * @param  boolean $isFilled
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function close($close = true){
        $this->_closed = (bool)$close;
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
     * Add points that need to be part of the polygon
     *
     * @param array $points
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function addPoints(array $points) {
        foreach($points as $point) {
           $this->addPoint($point);
        }
    
        return $this;
    }

    /**
     * Add a point that needs to be part of the polygon
     *
     * @param Glitch_Image_Point|array $point The point
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function addPoint($point) {
        if($point instanceof Glitch_Image_Point) {
           $this->_points[] = $point;
        } elseif(is_array($point)) {
            $this->_points[] = new Glitch_Image_Point($point[0],$point[1]);
        } else {
            // require_once 'Zend/Exception.php';
            throw new Zend_Exception('A point can only be an array, or an instanceof Glitch_Image_Point');
        }
    }

    /**
     * Get the points on which the polygon is drawn
     *
     * @return array points of the polygon
     */
    public function getPoints() {
       return $this->_points;
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
