<?php
// require_once 'Zend/Image/Action/ActionAbstract.php';
// require_once 'Zend/Image/Point.php';

/**
 * @todo Make all properties protected, then implement a __get() and __set()
 * in order to enable data validation
 *
 */
class Glitch_Image_Action_DrawLine extends Glitch_Image_Action_ActionAbstract {
    /*
     * The point to start the line at
     *
     * @var Glitch_Image_Point _poinstStart
     */
    protected $_pointStart;

    /*
     * The point to end the line at
     *
     * @var Glitch_Image_Point _poinstEnd
     */
    protected $_pointEnd;

    /*
     * The thickness of the line
     *
     * @var integer thickness
     */
    protected $_strokeWidth = 1;

    /*
     * Determines if the line is filled
     *
     * @var boolean filled
     */
    protected $_filled = true;

    /*
     * The color of the line
     *
     * @var Glitch_Image_Color color
     */
    protected $_strokeColor;

    /**
     * The alpha channel of the stroke
     *
     * @var float alpha
     */
    protected $_strokeAlpha = 1;

    /*
     * The name of this action
     */
    const NAME  = 'DrawLine';

    /**
     * Parse the inital options for the line
     *
     * @param  array $options An array with the options of the line
     */
    public function __construct($options=array()) {
        $this->_pointStart = new Glitch_Image_Point();
        $this->_pointEnd = new Glitch_Image_Point();

        return parent::__construct($options);
    }


    /**
     * Parse the inital options for the line
     *
     * @param  array $options An array with the options of the line
     */
    public function addOptions($options = array()) {
        $options = $this->_parseOptions($options);

        foreach($options as $key => $value) {
            switch($key) {
                case 'strokeColor':
                    $this->setStrokeColor($value);
                    break;
                case 'strokeWidth':
                    $this->setStrokeWidth($value);
                    break;
                case 'strokeAlpha':
                    $this->setStrokeAlpha($value);
                    break;
                case 'startX':
                    $this->setStartX($value);
                    break;
                case 'startY':
                    $this->setStartY($value);
                    break;
                case 'endX':
                    $this->setEndX($value);
                    break;
                case 'endY':
                    $this->setEndY($value);
                    break;
            }
        }
        
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
    
    public function getThickness() {
        return $this->_thickness;
    }

    /**
     * Determine if the line is filled
     *
     * @param  boolean $isFilled
     * @return this
     */
    public function setFilled($isFilled=true) {
        $this->_filled = (bool) $isFilled;
        return $this;
    }
    
    public function isFilled() {
        return $this->_filled;
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
     * Set the starting coordinates of the line
     *
     * @param Glitch_Image_Point|integer $param1 A point or coordinate to start at
     * @param integer $y (Optional)            The Y-coordinate to start at
     * @return this
     */
    public function from($param1,$y = null){
        if($param1 instanceof Glitch_Image_Point) {
            $this->_pointStart = $param1;
        } else {
            $this->_pointStart->setLocation($param1,$y);
        }

        return $this;
    }

    /**
     * Set the ending coordinates of the line
     *
     * @param Glitch_Image_Point|integer $param1 A point or coordinate to end at
     * @param integer $y (Optional)            The Y-coordinate to end at
     * @return this
     */
    public function to($param1,$y=null){
        if($param1 instanceof Glitch_Image_Point) {
            $this->_pointEnd = $param1;
        } else {
            $this->_pointEnd->setLocation($param1,$y);
        }

        return $this;
    }

    /**
     * Set the starting X-coordinate of the line
     *
     * @param integer $x The X-coordinate to start at
     * @return this
     */
    public function setStartX($x) {
       $this->_pointStart->setX($x);
       return $this;
    }

    /**
     * Set the starting Y-coordinate of the line
     *
     * @param integer $y The Y-coordinate to start at
     * @return this
     */
    public function setStartY($y) {
       $this->_pointStart->setY($y);
       return $this;
    }

    /**
     * Set the ending X-coordinate of the line
     *
     * @param integer $x The X-coordinate to end at
     * @return this
     */
    public function setEndX($x) {
        $this->_pointEnd->setX($x);
        return $this;
    }

    /**
     * Set the ending Y-coordinate of the line
     *
     * @param integer $y The Y-coordinate to end at
     * @return this
     */
    public function setEndY($y) {
        $this->_pointEnd->setY($y);
        return $this;
    }

    /**
     * Set the alpha channel
     *
     * @param integer $alpha The alpha channel
     * @return this
     */
    public function setAlpha($alpha) {
        $this->_alpha = (int)$alpha;
        return $this;
    }
    
    public function getAlpha() {
        return $this->_alpha;
    }
    
    /**
     * Get line color
     *
     * @return Glitch_Image_Color Stroke color
     */
    public function getColor() {
        if (null == $this->_color){
            $this->setColor(0, 0, 0);
        }

        return $this->_color;
    }

    /**
     * Set line color
     * @param Glitch_Image_Color|string $color
     * @return Glitch_Image_Action_DrawPolygon Provides fluent interface
     */
    public function setColor($color, $green = null, $blue = null){
        if (!$color instanceof Glitch_Image_Color) {
            $color = new Glitch_Image_Color($color, $green, $blue);
        }

        $this->_color = $color;
        return $this;
    }

    /**
     * Set the coordinates of the line
     *
     * @param integer $xStart   The X-coordinate to start at
     * @param integer $yStart   The Y-coordinate to start at
     * @param integer $xEnd     The X-coordinate to end at
     * @param integer $yEnd     The Y-coordinate to end at
     * @return this
     */
    public function setCoords($xStart, $yStart, $xEnd, $yEnd) {
        $this->_pointStart->setLocation($xStart, $yStart);
        $this->_pointEnd->setLocation($xEnd, $yEnd);
        return $this;
    }

    /**
     * Get the starting point
     *
     * @return Starting point
     */
    public function getPointStart() {
       return $this->_pointStart;
    }

    /**
     * Get the ending point
     *
     * @return ending point
     */
    public function getPointEnd() {
       return $this->_pointEnd;
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
