<?php
// require_once 'Zend/Image/Action/ActionAbstract.php';
// require_once 'Zend/Image/Point.php';

class Glitch_Image_Action_Resize extends Glitch_Image_Action_ActionAbstract {

    const NAME  = 'Resize';

    const TYPE_ABSOLUTE = 'absolute';
    const TYPE_RELATIVE = 'relative';

    protected $_xAmount;
    protected $_yAmount;
    
    protected $_yAmountCalculated;
    protected $_xAmountCalculated;

    protected $_xType;
    protected $_yType;

    protected $_filter;

    protected $_constrainProportions = false;

    public function addOptions($options = array()) {
        $options = $this->_parseOptions($options);
        
        foreach($options as $key => $value) {
            switch($key) {
                case 'xAmount':
                    $this->setXAmount($value);
                    break;
                case 'yAmount':
                    $this->setYAmount($value);
                    break;
                case 'xType':
                    $this->setXType($value);
                    break;
                case 'yType':
                    $this->setYType($value);
                    break;
                case 'type':
                    $this->setXType($value);
                    $this->setYType($value);
                    break;
                case 'filter':
                    $this->setFilter($value);
                    break;
                case 'constrain':
                    $this->constrainProportions($value);
                    break;

            }
        }

        return $this;
    }

    public function setFilter($filter){
        $this->_filter = $filter;

        return $this;
    }

    public function getFilter(){
        return $this->_filter;
    }

    public function setXAmount($value){
        $this->_xAmount = (int) $value;

        return $this;
    }

    public function setYAmount($value){
        $this->_yAmount = (int) $value;

        return $this;
    }

    public function getXAmount(){
        return $this->_xAmount;
    }

    public function getYAmount(){
        return $this->_yAmount;
    }

    public function setXType($value){
        $this->_xType = $value;

        return $this;
    }

    public function setYType($value){
        $this->_yType = $value;

        return $this;
    }

    public function getXType(){
        return $this->_xType;
    }

    public function getYType(){
        return $this->_yType;
    }

    public function getName() {
        return 'Resize';
    }

    public function constrainProportions($constrain){
        $this->_constrainProportions = $constrain;

        return $this;
    }

    public function hasConstrainedProportions(){
        return $this->_constrainProportions;
    }
    
    public function getXAmountCalculated() {
        return $this->_xAmountCalculated;
    }
    
    public function getYAmountCalculated() {
        return $this->_yAmountCalculated;
    }
    
    public function perform(Glitch_Image_Adapter_AdapterAbstract $adapter) {
        $newX = $this->getXAmount();
        $newY = $this->getYAmount();

        if (null === $newX && null === $newY){
            throw new Glitch_Image_Action_Exception (
                'No dimensions for resizing were specified, '
              . 'at least one dimension should be specified.');
        }
        
        $constrain = $this->hasConstrainedProportions();

        if (null === $newX) {
            if ($constrain) {
                $newX = 0;
            } else{
                $newX = $adapter->getWidth();
            }
        } elseif (self::TYPE_RELATIVE == $this->getXType()) {
            $ratio = $newY / $adapter->getHeight();
            $newX = $adapter->getWidth() * $ratio;  
        }
        
        if (null === $newY) {
            if ($constrain) {
                $newY = 0;
            } else {
                $newY = $adapter->getHeight();
            }
        } elseif (self::TYPE_RELATIVE == $this->getYType()) {
            $ratio = $newX / $adapter->getWidth();
            $newY = $adapter->getHeight() * $ratio;           
        }
        
        $this->_yAmountCalculated = $newY;
        $this->_xAmountCalculated = $newX;
        
        return parent::perform($adapter);
    }
}
