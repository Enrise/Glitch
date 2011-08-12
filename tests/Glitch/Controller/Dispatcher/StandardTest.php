<?php


require_once dirname(__FILE__) . '/../Request/_data/RestMock.php';

class Glitch_Controller_Dispatcher_StandardTest
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

    /**
     * This test suffices because _formatName() is tested
     * by our beloved friends at Zend
     */
    public function testFormatModuleNameProxiesToFormatName()
    {
        $dispatcher = new Glitch_Controller_Dispatcher_StandardTest_formatModuleNameProxiesToFormatName();

        try {
            $dispatcher->formatModuleName('unitT3st');
            $this->fail();
        } catch(Exception $e) {
            $this->assertEquals($e->getMessage(), 'success');
        }
    }

    public function testFormatControllerName()
    {
        $dispatcher = new Glitch_Controller_Dispatcher_Standard_Mock();

        $dispatcher->setCurModuleName('testMod-ule');
        $this->assertEquals(
            $dispatcher->formatControllerName('foobar'),
            'TestmodUle_Controller_Foobar'
        );
    }

    public function testLoadClass()
    {
        $dispatcher = new Glitch_Controller_Dispatcher_Standard_Mock();
        try {
            $dispatcher->loadClass('___foobarro-o');
            $this->fail('exception expected');
        } catch(Zend_Controller_Dispatcher_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Cannot load controller class "___foobarro-o"');
        }

        $class = 'Glitch_Controller_Dispatcher_StandardTest';
        $this->assertEquals($dispatcher->loadClass($class), $class);
    }

    public function testIsDispatchable()
    {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();

        $request = new Glitch_Controller_Request_RestMock(
                       'http://example.net/decision', $bootstrap);

        $class = 'Glitch_Controller_Dispatcher_StandardTest_'
                .'isDispatchableProxiesToGetControllerClass';

        $dispatcher = new $class();
        $request->setParam('return', true);
        $this->assertTrue($dispatcher->isDispatchable($request));

        $request->setParam('return', false);
        $this->assertFalse($dispatcher->isDispatchable($request));
    }
}

class Glitch_Controller_Dispatcher_StandardTest_formatModuleNameProxiesToFormatName
    extends Glitch_Controller_Dispatcher_Standard
{
    protected function _formatName($unformatted, $isAction = false) {
        if($unformatted == 'unitT3st' && !$isAction) {
            throw new Exception('success');
        }
    }
}

class Glitch_Controller_Dispatcher_StandardTest_isDispatchableProxiesToGetControllerClass
    extends Glitch_Controller_Dispatcher_Standard
{
    public function isDispatchable(Zend_Controller_Request_Abstract $request)
    {
        return $request->getParam('return');
    }
}

class Glitch_Controller_Dispatcher_Standard_Mock
    extends Glitch_Controller_Dispatcher_Standard
{
    public function setCurModuleName($name)
    {
        $this->_curModule = $name;
    }
}