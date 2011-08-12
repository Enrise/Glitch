<?php

require __DIR__ . '/_files/FrontMock.php';

/**
 * @category   Glitch
 * @package    Glitch_Controller
 * @subpackage UnitTests
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Glitch_Controller
 * @group      Glitch_Controller_Front
 */
class Glitch_Controller_FrontTest
    extends PHPUnit_Framework_TestCase
{

    protected static $_includePath;

    private $_appConfig = array(
        'pluginPaths' =>
            array('Glitch_Application_Resource' => 'Glitch/Application/Resource'),
        'resources' => array(
            'modules' => true,
            'frontController' => array(
                'errorHandler' => array('module' => 'error'),
                'controllerDirectory' =>
                    array('decisionmodule' => 'seeconstructor')),
            'router' => array(
                'routes' => array('decisionRest' =>
                    array('route' => 'decision/',
                          'type' => 'Glitch_Controller_Router_Route_Rest',
                          'defaults' => array('module' => 'decisionmodule'))),
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

    protected $_controller = null;

    public static function setUpBeforeClass()
    {
        self::$_includePath = get_include_path();
    }

    public function tearDown()
    {
        Glitch_Controller_FrontMock::resetHard();
        set_include_path(static::$_includePath);
        Glitch_Console_Getopt::reset();
    }

    public function setUp()
    {
        $this->_controller = Zend_Controller_Front::getInstance();
        $this->_controller->resetInstance();
        $this->_controller->setParam('noErrorHandler', true)
                          ->setParam('noViewRenderer', true)
                          ->returnResponse(true)
                          ->throwExceptions(false);
        Zend_Controller_Action_HelperBroker::resetHelpers();
    }

    public function testResetInstance()
    {
        $this->assertInstanceOf('Glitch_Controller_Front', $this->_controller);
        $this->assertInstanceOf('Zend_Controller_Front', $this->_controller);

        $this->_controller->setParam('foo', 'bar');
        $this->_controller->setRouter($router = new Zend_Controller_Router_Rewrite());
        $this->_controller->setDispatcher($dispatcher = new Zend_Controller_Dispatcher_Standard());

        $this->_controller->resetInstance();

        $this->assertEquals($this->_controller->getParam('foo'), 'bar');
        $this->assertEquals($this->_controller->getRouter(), $router);
        $this->assertEquals($this->_controller->getDispatcher(), $dispatcher);
    }

    public function testDispatch1()
    {
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/_files/');
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();

        $response = new Glitch_Controller_Response_Rest();
        $request = new Glitch_Controller_Request_RestMock(
                       'http://example.net/decision/location/5/element/1/defects',
                    $bootstrap,
                    array('REQUEST_METHOD' => 'PUT'));

        $router = $bootstrap->getResource('router');

        $dispatcher = new Glitch_Controller_Dispatcher_Rest();
        $this->_controller->setParam('noErrorHandler', false);
        $this->_controller->setDispatcher($dispatcher);

        $this->_controller->returnResponse(true);
        $response = $this->_controller->dispatch($request, $response);

        $this->assertEquals($response->getBody(), <<<'EOD'
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <unit>test</unit>
</zend-config>

EOD
        );
   }

    public function testDispatch2()
    {
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/_files/');

        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();
        $this->_controller->setParam('bootstrap', $bootstrap);

        $response = new Glitch_Controller_Response_Rest();

        $router = $bootstrap->getResource('router');

        $dispatcher = new Glitch_Controller_Dispatcher_Rest();
        $this->_controller->setParam('noErrorHandler', true);
        $this->_controller->setDispatcher($dispatcher);

        $this->_controller->returnResponse(true);
        $this->_controller->dispatch(null, $response);

        $errorHandler = $this->_controller->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        $this->assertFalse($errorHandler);
   }

    public function testDispatch3()
    {
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/_files/');

        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();
        $this->_controller->setParam('bootstrap', $bootstrap);

        $response = new Glitch_Controller_Response_Rest();

        $getopt = new Glitch_Console_Getopt(
            array('request|r=s' => 'format module.controller.action',
                  'params|p=s' => 'format key=value[&key=value]'),
            array('--request=foo.bar.baz')
        );
        $getopt->parse();

        $router = new Glitch_Controller_Router_Cli();
        $getopt->saveInstance($router::CONSOLE_GETOPT_KEY);

        $this->_controller->setRouter($router);

        $dispatcher = new Glitch_Controller_Dispatcher_Standard();
        $this->_controller->setParam('noErrorHandler', true);
        $this->_controller->setDispatcher($dispatcher);

        $this->_controller->returnResponse(true);
        $this->_controller->dispatch(null, $response);

        $errorHandler = $this->_controller->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        $this->assertFalse($errorHandler);
        $this->assertTrue($this->_controller->getDispatcher() === $dispatcher);
   }

}
