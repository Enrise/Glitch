<?php
// require_once 'Zend/Image/Action/ActionAbstract.php';
// require_once 'Zend/Image/Point.php';

/**
 * Crop prototype
 * Should support definition of anything known to form a crop, i.e.
 * top left corner (crop to image edge)   DONE
 * top left corner + w + h (crop box)     DONE
 * bottom right corner + w + h (crop box)
 * calulation of coords from subsequent calls to setWidth/setHeight
 */

class Glitch_Image_Action_Crop extends Glitch_Image_Action_ActionAbstract
{

    /**
     * The x coordinate of the top left corner of the crop's bounding box
     *
     * @var int x
     */
    protected $_x;

    /**
     * The y coordinate of the top left corner of the crop's bounding box
     *
     * @var int y
     */
    protected $_y;

    /**
     * The x coordinate of the top left corner of the crop's bounding box
     *
     * @var int x
     */
    protected $_x1;

    /**
     * The y coordinate of the top left corner of the crop's bounding box
     *
     * @var int y
     */
    protected $_y1;

    /**
     * The width of the crop
     *
     * @var int width
     */
    protected $_width;

    /**
     * The height of the crop
     *
     * @var int height
     */
    protected $_height;


    public function addOptions($options = array()) {
        $options = $this->_parseOptions($options);

        foreach($options as $key => $value) {
            switch($key) {
                case 'x':
                    $this->setStrokeWidth($value);
                    break;
                case 'y':
                    $this->addPoints($value);
                    break;
                case 'width':
                    $this->setWidth($value);
                    break;
                case 'height':
                    $this->setHeight($value);
                    break;
            }
        }

        return $this;
    }

    /**
     *
     *
     * @return int
     */
    public function getX() {
        return $this->_x;
    }

    /**
     * Set x coordinate of top left of bounding box
     *
     * @param  int $x
     * @return Glitch_Image_Action_Crop Provides fluent interface
     */
    public function setX($x){
        $this->_x = $x;

        $this->_calculateWidth();

        return $this;
    }

    /**
     *
     *
     * @return int
     */
    public function getY() {
        return $this->_y;
    }

    /**
     * Set y coordinate of top left corner of bounding box
     *
     * @param  int $y
     * @return Glitch_Image_Action_Crop Provides fluent interface
     */
    public function setY($y){
        $this->_y = $y;

        $this->_calculateHeight();

        return $this;
    }

    /**
     *
     *
     * @return int
     */
    public function getX1() {
        return $this->_x1;
    }

    /**
     * Set x coordinate of top left of bounding box
     *
     * @param  int $x
     * @return Glitch_Image_Action_Crop Provides fluent interface
     */
    public function setX1($x){

        $this->_x1 = $x;

        $this->_calculateWidth();

        return $this;
    }

    /**
     *
     *
     * @return int
     */
    public function getY1() {
        return $this->_y1;
    }

    /**
     * Set y coordinate of top left corner of bounding box
     *
     * @param  int $y
     * @return Glitch_Image_Action_Crop Provides fluent interface
     */
    public function setY1($y){
        $this->_y1 = $y;

        $this->_calculateHeight();

        return $this;
    }

    /**
     *
     * @param int $x
     * @param int $y
     * @param int $x1
     * @param int $y1
     * @return Glitch_Image_Action_Crop Provides fluent interface
     */
    public function setBoundingBox($x, $y, $x1, $y1){

        $this->setX($x)
             ->setY($y)
             ->setX1($x1)
             ->setY1($y1);

        return $this;
    }

    /**
     *
     * @return int
     */
    public function getWidth() {
        return $this->_width;
    }

    /**
     * Set width of bounding box
     *
     * @param  int $width
     * @return Glitch_Image_Action_Crop Provides fluent interface
     */
    public function setWidth($width){
        $this->_width = $width;

        return $this;
    }

    /**
     *
     * @return int|false Width or false if not enough information known
     */
    protected function _calculateWidth(){

        if (null === $this->_x || null === $this->_x1){
            return false;
        }

        if ($this->_x >= $this->_x1){
            $this->_width = $this->_x - $this->_x1;
        }else{
            $this->_width = $this->_x1 - $this->_x;
        }

        return $this->_width;
    }

    /**
     *
     *
     * @return int
     */
    public function getHeight() {
        return $this->_height;
    }

    /**
     * Set height of bounding box
     *
     * @param  int $height
     * @return Glitch_Image_Action_Crop Provides fluent interface
     */
    public function setHeight($height){
        $this->_height = $height;

        return $this;
    }

    /**
     *
     * @return int|false Height or false if not enough information known
     */
    protected function _calculateHeight(){

        if (null === $this->_y || null === $this->_y1){
            return false;
        }

        if ($this->_y >= $this->_y1){
            $this->_height = $this->_y - $this->_y1;
        } else {
            $this->_height = $this->_y1 - $this->_y;
        }

        return $this->_height;
    }

    public function getName() {
        return 'Crop';
    }
}
