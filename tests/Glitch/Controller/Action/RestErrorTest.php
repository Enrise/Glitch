<?php

require_once dirname(__FILE__) . '/../Request/_data/RestTestCaseMock.php';

class Glitch_Controller_Action_RestErrorTest
    extends PHPUnit_Framework_TestCase
{
    
    private $_appConfig = array(
        'pluginPaths' => 
            array('Glitch_Application_Resource' => 'Glitch/Application/Resource'),
    	'resources' => array('router' => array(
            'routes' => array('decisionRest' =>
                array('route' => 'decision/',
                      'type' => 'Glitch_Controller_Router_Route_Rest',
                      'defaults' => array('module' => 'decision'))),
            'restmappings' => 
                array('locations'   => array('name' => 'location',
                                             'isCollection' => true)))));
   
    protected $_controller;
    
    protected $_request;
    
    protected function setUp()
    {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
       
        $response = new Glitch_Controller_Response_Rest();
        $this->_request = new Glitch_Controller_Request_RestMock(
       	 			'http://example.net/decision/',
                    $bootstrap);
                    
        $this->_controller = new Glitch_Controller_Action_RestErrorMock(
                                $this->_request, $response, array('bootstrap' => $bootstrap)
                             );
    }
                
    public function testActionSelection()
    {
        $controller = $this->_controller;
        
        $this->assertInstanceOf('Glitch_Controller_Action_Rest', $controller);
        $this->assertEquals('restAction', $controller->dispatch($this->_request));
        
        $request = new Zend_Controller_Request_Http('http://example.net/decision/');
        $this->assertEquals('errorAction', $controller->dispatch($request));
    }
    
    public function testErrorActionWithDisplayableMsg()
    {
        $controller = $this->_controller;
        $response = $controller->getResponse();
        $exception = new Glitch_Exception_Message('unittest', 543);
        
        $controller->getRequest()->setParam('error_handler', 
            (object) array('exception' => $exception));

        $this->assertEquals(
                array('data' => array('message' =>  'unittest', 'code' => 543)),
                $controller->restAction()
        );
        $this->assertEquals(543, $response->getHttpResponseCode());
        $this->assertTrue($response->renderBody());
    }
    
    public function testErrorActionWithoutDisplayableMsg()
    {
        $controller = $this->_controller;
        $response = $controller->getResponse();
        $exception = new Glitch_Exception('un1tt38t', 345);
        
        $controller->getRequest()->setParam('error_handler', 
            (object) array('exception' => $exception));

        $this->assertEquals(
                array('data' => array('message' =>  '', 'code' => 345)),
                $controller->restAction()
        );
        $this->assertEquals(345, $response->getHttpResponseCode());
        $this->assertFalse($response->renderBody());
    }
}   

class Glitch_Controller_Action_RestErrorMock extends Glitch_Controller_Action_RestError { }
