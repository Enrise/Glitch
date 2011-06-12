<?php

class Glitch_Controller_Request_RestTestCaseMock
    extends Glitch_Controller_Request_RestTestCase
{
    private $origServer;
    
    public function __construct($uri, 
                                Glitch_Application_Bootstrap_Bootstrap $bootstrap = null)
    {
        $this->origServer = $_SERVER;
        
        if($bootstrap != null) {
            $this->_bootstrap = $bootstrap;
        }
        
        parent::__construct($uri);
    }
    
    public function __destruct()
    {
        $_SERVER = $this->origServer;
    }
}
