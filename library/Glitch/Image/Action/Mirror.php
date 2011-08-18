<?php
// require_once 'Zend/Image/Action/ActionAbstract.php';
// require_once 'Zend/Image/Point.php';

/**
 *
 */
class Glitch_Image_Action_Mirror extends Glitch_Image_Action_ActionAbstract
{

    const NAME  = 'Mirror';

    protected $_flip = false;

    protected $_flop = false;

    public function addOptions($options = array()) {
        $options = $this->_parseOptions($options);

        foreach($options as $key => $option) {
            $option = strtoupper($option);
            $key = strtoupper($key);

            switch($option) {
                case 'FLIP':
                case 'Y':
                case 'VERTICAL':
                    $this->flip(true);
                    break;

                case 'FLOP':
                case 'X':
                case 'HORIZONTAL':
                    $this->flop(true);
                    break;

                case 'XY':
                case 'YX':
                case 'BOTH':
                case 'B0TH':
                    $this->flop(true);
                    $this->flip(true);
                    break;
            }

        }

        return $this;
    }

    public function flip($do = null) {
        if($do === null) {
            return $this->_flip;
        }

        $this->_flip = (bool) $do;
        return $this;
    }

    public function flop($do = null) {
        if($do === null) {
            return $this->_flop;
        }

        $this->_flop = (bool) $do;
        return $this;
    }

    public function setOptions($options = array()) {
        $this->flip(false);
        $this->flop(false);

        $this->addOptions($options);

        return $this;
    }


    /**
     * @param string $axis
     * @return Glitch_Image_Action_Flip Provides fluent interface
     */
    public function setAxis($axis){
        $this->setOptions($axis);

        if(!$this->flop() && !$this->flip()) {
            // require_once 'Zend/Image/Exception.php';
            throw new Glitch_Image_Exception('Invalid axis specified');
        }

        return $this;
    }

    public function perform(Glitch_Image_Adapter_AdapterAbstract $adapter) {
        if(!$this->flop() && !$this->flip()) {
            // require_once 'Zend/Image/Exception.php';
            throw new Glitch_Image_Exception('No axis specified to mirror');
        }

        return parent::perform($adapter);
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
