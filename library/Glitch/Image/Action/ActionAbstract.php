<?php
// require_once 'Glitch/Image/Action/ActionInterface.php';
// require_once 'Zend/Loader.php';

abstract class Glitch_Image_Action_ActionAbstract
    implements Glitch_Image_Action_ActionInterface
{

    public function __construct($options = array()) {
        $this->addOptions($options);
    }

    protected function _parseOptions($options) {
        if(is_object($options)) {
            if ($options instanceof Zend_Config ||
                method_exists($options,'toArray'))
            {
                $options = $options->toArray();
            }
        }

        return (array)$options;
    }

    public function perform(Glitch_Image_Adapter_AdapterAbstract $adapter = null) {
        if(null === $adapter) {
            throw new Glitch_Image_Action_Exception('No adapter given.');
        }

        $name = 'Glitch_Image_Adapter_' . $adapter->getName() . '_Action_' . $this->getName();
        Zend_Loader::loadClass ( $name );

        $actionObject = new $name ( );
        return $actionObject->perform ( $adapter, $this);
    }

}
