<?php

class Glitch_Controller_Request_RestMock
    extends Glitch_Controller_Request_Rest
{
    private $origServer;
    
    public function __construct($uri, 
                                Glitch_Application_Bootstrap_Bootstrap $bootstrap = null,
                                $serverArgs = array())
    {
        $this->origServer = $_SERVER;
        
        foreach($serverArgs as $key => $value) {
            $this->setServerKey($key, $value);
        }
        
        if($bootstrap != null) {
            $this->_bootstrap = $bootstrap;
        }
        
        parent::__construct($uri);
    }
    
    public function __destruct()
    {
        $_SERVER = $this->origServer;
    }
    
    public function setServerKey($key, $value) {
        $_SERVER[$key] = $value;
    }
}
