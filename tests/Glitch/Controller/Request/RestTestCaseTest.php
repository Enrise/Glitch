<?php

require_once dirname(__FILE__) . '/_data/RestTestCaseMock.php';

class Glitch_Controller_Request_RestTestCaseTest
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
    
    public function testHeaderManipulation()
    {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
       
        $request = new Glitch_Controller_Request_RestTestCaseMock(
                       'http://example.net/decision/location/5/defects', $bootstrap);
        
        $request->setHeader('foo', 'bar');
        $request->setHeader('bar', 'baz');
        $_SERVER['HTTP_BAZ'] = 'foo';
        
        $this->assertEquals('bar', $request->getHeader('foo'));
        $this->assertEquals('baz', $request->getHeader('bar'));
        $this->assertEquals('foo', $request->getHeader('baz'));
        
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'),
                            $request->getHeaders());
     }
     
     public function testMethodManipulation()
     {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
       
        $request = new Glitch_Controller_Request_RestTestCaseMock(
                       'http://example.net/decision/location/5/defects', $bootstrap);
        
        $this->assertEquals('GET', $request->getMethod()); // should default to GET
        
        $request->setMethod('DELETE');
        $this->assertEquals('DELETE', $request->getMethod());
        
        $request->setMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
    }
}
