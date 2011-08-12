<?php

class Glitch_Controller_Response_Renderer_HelperBroker
{
    protected $_helpers;

    protected static $_staticHelpers;

    protected $_shortcuts = array(
        '_' => 'translate'
    );

    public function __construct($getStatic = true)
    {
        if($getStatic) {
            $this->_helpers = static::_getStaticHelpers();
        } else {
            $this->_helpers = static::_initStaticHelpers();
        }
    }

    public function __call($method, $args)
    {
        $helper = $this->_helpers[$method];
        return call_user_func_array($helper, $args);
    }

    public static function __callstatic($method, $args)
    {
        $helpers = static::_getStaticHelpers();
        return call_user_func_array($helpers[$method], $args);
    }

    protected static function _initStaticHelpers()
    {
        return array(
        'escape' =>
            function($string)
            {
                return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
            },
         'translate' =>
            function($string)
            {
                $options = func_get_args();
                array_shift($options);

                return Zend_Layout::getMvcInstance()
                            ->getView()
                                ->getHelper('translate')->translate($string, $options);
            }
        );
    }

    protected static function _getStaticHelpers()
    {
        if (static::$_staticHelpers == null) {
            static::$_staticHelpers = static::_initStaticHelpers();
        }

        return static::$_staticHelpers;
    }

    /**
     * @return array
     * @throws \RuntimeException If a defined shortcut is no closure
     */
    public function getShortCuts()
    {
        $out = array();
        foreach ($this->_shortcuts as $name => $value) {
            $helper = $this->_helpers[$value];
            if (!is_callable($helper) || !is_object($helper)) {
                throw new \RuntimeException(
                    'All shortcut helpers must be closures but '.$name.' wasn\'t'
                );
            }

            $out[$name] = $this->_helpers[$value];
        }

        return $out;
    }
}
