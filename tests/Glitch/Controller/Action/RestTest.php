<?php

require_once dirname(__FILE__) . '/../Request/_data/RestTestCaseMock.php';

class Glitch_Controller_Action_RestTest
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
                                              'isCollection' => true),
                      'location'    => array('name' => 'location'),
                      'elements'    => array('name' => 'element',
                                             'isCollection' => true),
                      'element'     => array('name' => 'element'),
                      'defects'     => array('name' => 'defect',
                                             'isCollection' => true),
                      'defect'      => array('name' => 'defect')))));
    
   
   public function testActionSelection()
   {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();
       
        $response = new Glitch_Controller_Response_Rest();
        $request = new Glitch_Controller_Request_RestMock(
       	 			'http://example.net/decision/location/5/defects',
                    $bootstrap,
                    array('REQUEST_METHOD' => 'POST'));
       
        $router = $bootstrap->getResource('router');
        $router->route($request);
        
        $controller = new Glitch_Controller_Action_RestMock(
                            $request, $response, array('bootstrap' => $bootstrap)
                     );
                     
        $this->assertInstanceOf('Zend_Controller_Action_Interface', $controller);
        $this->assertInstanceOf('Zend_Controller_Action', $controller);
        
        $this->assertEquals('collectionPostAction', $controller->getActionMethod($request));
        $this->assertEquals('collectionPostAction', $controller->dispatch($request));
        
        $response = new Glitch_Controller_Response_Rest();
        $request = new Glitch_Controller_Request_RestMock(
       	 			'http://example.net/decision/location/5/defect/4',
                    $bootstrap,
                    array('REQUEST_METHOD' => 'PUT'));
        $this->assertEquals('resourcePutAction', $controller->getActionMethod($request));
        $this->assertEquals('resourcePutAction', $controller->dispatch($request));
    }
   
    public function testExceptions()
    {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();
       
        $response = new Glitch_Controller_Response_Rest();
        $request = new Glitch_Controller_Request_RestMock(
       	 			'http://example.net/decision/location/5/defects',
                    $bootstrap,
                    array('REQUEST_METHOD' => 'POST'));
       
        $router = $bootstrap->getResource('router');
        $router->route($request);
        
        $controller = new Glitch_Controller_Action_RestMock(
                            $request, $response, array('bootstrap' => $bootstrap)
                     );

        $exceptions = array(array('msg' => 'Requested resource could not be found',
                                  'code' => 404,
                                  'method' => 'notFoundException'),
                            array('msg' => 'Incorrect format specified',
                                  'code' => 406,
                                  'method' => 'notAcceptedException'));
        
        $methods = array('Put', 'Post', 'Get', 'Delete', 'Options', 'foobar');
        foreach(array('resource', 'collection') as $type) {
            foreach($methods as $method) {
                $methodName = $type . $method . 'Action';
                $exceptions[] = array('code' => 501, 
                					'msg' => 'Requested action '. $methodName .' not implemented',
                                    'method' => $methodName);
            }
        }
        
        foreach($exceptions as $exception) {
            try {
                $controller->{$exception['method']}();
                $this->fail('Exception expected');
            } catch(Glitch_Exception_Message $e) {
                $this->assertEquals($exception['msg'], $e->getMessage());
                $this->assertEquals($exception['code'], $e->getCode());
            }
        }
    }
    
    public function testPassthrough()
    {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();
       
        $response = new Glitch_Controller_Response_Rest();
        $request = new Glitch_Controller_Request_RestMock(
       	 			'http://example.net/decision/location/5/defects',
                    $bootstrap,
                    array('REQUEST_METHOD' => 'POST'));
       
        $router = $bootstrap->getResource('router');
        $router->route($request);
        
        $this->assertTrue(Glitch_Controller_Action_RestMock::passThrough($request, ''));
    }
}   

class Glitch_Controller_Action_RestMock extends Glitch_Controller_Action_Rest { }