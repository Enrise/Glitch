<?php


require_once dirname(__FILE__) . '/../Request/_data/RestMock.php';

class Glitch_Controller_Dispatcher_RestTest
    extends PHPUnit_Framework_TestCase
{
    private $_appConfig = array(
        'pluginPaths' =>
            array('Glitch_Application_Resource' => 'Glitch/Application/Resource'),
        'resources' => array(
            'modules' => true,
            'frontController' => array('controllerDirectory' =>
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
                          'defect'      => array('name' => 'defect'),
                          'failpassthru'=> array('name' => 'failpassthru')))));

    public function __construct()
    {
        $this->_appConfig['resources']['frontController']['controllerDirectory']
                                                                ['decisionModule']
            = __DIR__ . '/_data/DecisionModule/Controller';

        return parent::__construct();
    }

    public function testDispatchThrowsException()
    {
        $response = new Glitch_Controller_Response_Rest();
        $request = new Zend_Controller_Request_Http('http://example.net/decision');

        $dispatcher = new Glitch_Controller_Dispatcher_Rest();

        try {
            $dispatcher->dispatch($request, $response);
            $this->fail('exception expected');
        } catch(Glitch_Controller_Exception $e) {
            $this->assertEquals(
                $e->getMessage(),
                'Request must be of type Glitch_Controller_Request_Rest but was Zend_Controller_Request_Http'
            );
        }
    }

    public function testDispatch()
    {
        $includePath = get_include_path();
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/_data/');

        try {
            $app = new Zend_Application('testing', $this->_appConfig);
            $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
            $bootstrap->bootstrap();

            $response = new Glitch_Controller_Response_Rest();
            $request = new Glitch_Controller_Request_RestMock(
                           'http://example.net/decision/location/5/element/1/defects',
                        $bootstrap,
                        array('REQUEST_METHOD' => 'PUT'));

            $router = $bootstrap->getResource('router');
            $router->route($request);

            $dispatcher = new Glitch_Controller_Dispatcher_Rest();
            $response->renderbody(false);
            $dispatcher->dispatch($request, $response);
            $response->renderBody(true);

            ob_start();
            $response->outputBody();
            $this->assertEmpty(ob_get_clean());

            $this->assertEquals($dispatcher->getResponse(), $response);
            $this->assertInstanceOf('Decisionmodule_Controller_Location_Element_Defect',
                                    $dispatcher->getLastController());
            $this->assertTrue($request->isDispatched());
            $this->assertEquals('collectionPutAction', $dispatcher->getLastActionMethod());
            $this->assertEquals('collectionPutAction', $request->getActionName());

            $response->renderBody(true);
            $dispatcher->dispatch($request, $response);

            ob_start();
            $response->outputBody();
            $this->assertEquals(ob_get_clean(), $this->_expectedDispatchBody );

            set_include_path($includePath);
        } catch(exception $e) {
            set_include_path($includePath);
            throw $e;
        }
    }
    
    public function testPreDispatch()
    {
        $includePath = get_include_path();
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/_data/');

        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();

        $response = new Glitch_Controller_Response_Rest();
        $request = new Glitch_Controller_Request_RestMock(
                    'http://example.net/decision/locations',
                    $bootstrap,
                    array('REQUEST_METHOD' => 'DELETE'));

        $router = $bootstrap->getResource('router');
        $router->route($request);

        $dispatcher = new Glitch_Controller_Dispatcher_Rest();
        $response->renderbody(false);
        $dispatcher->dispatch($request, $response);
        $response->renderBody(true);

        ob_start();
        $response->outputBody();
        $this->assertEmpty(ob_get_clean());

        $this->assertEquals($dispatcher->getResponse(), $response);
        $this->assertInstanceOf('Decisionmodule_Controller_Location', $dispatcher->getLastController());
        $this->assertClassHasAttribute('preDispatch', 'Decisionmodule_Controller_Location');
        $this->assertTrue($dispatcher->getLastController()->preDispatch);
    }

    public function testPostDispatch()
    {
        $includePath = get_include_path();
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/_data/');

        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();

        $response = new Glitch_Controller_Response_Rest();
        $request = new Glitch_Controller_Request_RestMock(
                    'http://example.net/decision/locations',
                    $bootstrap,
                    array('REQUEST_METHOD' => 'DELETE'));

        $router = $bootstrap->getResource('router');
        $router->route($request);

        $dispatcher = new Glitch_Controller_Dispatcher_Rest();
        $response->renderbody(false);
        $dispatcher->dispatch($request, $response);
        $response->renderBody(true);

        ob_start();
        $response->outputBody();
        $this->assertEmpty(ob_get_clean());

        $this->assertEquals($dispatcher->getResponse(), $response);
        $this->assertInstanceOf('Decisionmodule_Controller_Location', $dispatcher->getLastController());
        $this->assertClassHasAttribute('postDispatch', 'Decisionmodule_Controller_Location');
        $this->assertTrue($dispatcher->getLastController()->postDispatch);
    }

    public function testExceptionIsThrownOnFailingPassthrough()
    {
        $includePath = get_include_path();
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/_data/');

        try {
            $app = new Zend_Application('testing', $this->_appConfig);
            $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
            $bootstrap->bootstrap();

            $response = new Glitch_Controller_Response_Rest();
            $request = new Glitch_Controller_Request_RestMock(
                           'http://example.net/decision/location/5/element/1/defect/2/failpassthru',
                        $bootstrap,
                        array('REQUEST_METHOD' => 'PUT'));

            $router = $bootstrap->getResource('router');
            $router->route($request);

            $dispatcher = new Glitch_Controller_Dispatcher_Rest();
            try {
                $dispatcher->dispatch($request, $response);
                $this->fail('Exception expected');
            } catch(Glitch_Controller_Exception $e) {
                $this->assertEquals('Passthrough method returned false', $e->getMessage());
            }

            set_include_path($includePath);
        } catch(exception $e) {
            set_include_path($includePath);
            throw $e;
        }
    }

    public function testRenderResponse()
    {
        $includePath = get_include_path();
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/_data/');

        try {
            $app = new Zend_Application('testing', $this->_appConfig);
            $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
            $bootstrap->bootstrap();

            $response = new Glitch_Controller_Response_Rest();
            $request = new Glitch_Controller_Request_RestMock(
                           'http://example.net/decision/location/',
                        $bootstrap,
                        array('REQUEST_METHOD' => 'DELETE'));
            $request->setParam('format', 'json');

            $router = $bootstrap->getResource('router');
            $router->route($request);

            $dispatcher = new Glitch_Controller_Dispatcher_Rest();
            $dispatcher->dispatch($request, $response);

            // This means subresponserender setting works
            // This means output format selector works
            // This means null output is subsituted with an array
            // This means format selector works
            $this->assertEquals('pass', $response->getBody());

            set_include_path($includePath);
        } catch(exception $e) {
            set_include_path($includePath);
            throw $e;
        }
    }

    public function testRenderResponseThrowsExceptionWithInvalidSubrenderer()
    {
        $includePath = get_include_path();
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/_data/');

        try {
            $app = new Zend_Application('testing', $this->_appConfig);
            $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
            $bootstrap->bootstrap();

            $response = new Glitch_Controller_Response_Rest();
            $request = new Glitch_Controller_Request_RestMock(
                           'http://example.net/decision/locations/',
                        $bootstrap,
                        array('REQUEST_METHOD' => 'DELETE'));

            $router = $bootstrap->getResource('router');
            $router->route($request);

            $dispatcher = new Glitch_Controller_Dispatcher_Rest();

            try {
                $dispatcher->dispatch($request, $response);
                $this->fail('Exception expected');
            } catch(Glitch_Controller_Exception $e) {
                $msg = 'A SubResponseRenderer was set but could not be located. '
                      .'Looked for "Decisionmodule/View/Script/Location/Collecti'
                      .'onDeleteAction.subbie.xml.phtml" in: '.get_include_path();

                $this->assertEquals($e->getMessage(), $msg);
            }

            set_include_path($includePath);
        } catch(exception $e) {
            set_include_path($includePath);
            throw $e;
        }
    }

    public function testCloneFromDispatcher()
    {
        $dispatcher = new Zend_Controller_Dispatcher_Standard();
        $dispatcher->setParams($params = array('foo' => 'bar', 'bar' => 'baz'));
        $dispatcher->setControllerDirectory('John', 'Doe');
        $dispatcher->setDefaultModule('foomodule');
        $dispatcher->setDefaultControllerName('foocontrollername');
        $dispatcher->setDefaultAction('fooaction');
        $dispatcher->setPathDelimiter('-----f00------');

        $dispatcher = Glitch_Controller_Dispatcher_Rest::cloneFromDispatcher($dispatcher);
        $this->assertInstanceof('Glitch_Controller_Dispatcher_Rest', $dispatcher);

        $this->assertEquals($dispatcher->getParams(), $params);
        $this->assertEquals($dispatcher->getControllerDirectory(), array('Doe' => 'John'));
        $this->assertEquals($dispatcher->getDefaultModule(), 'foomodule');
        $this->assertEquals($dispatcher->getDefaultControllerName(), 'foocontrollername');
        $this->assertEquals($dispatcher->getDefaultAction(), 'fooaction');
        $this->assertEquals($dispatcher->getPathDelimiter(), '-----f00------');
    }

    private $_expectedDispatchBody = <<<'EOD'
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <unit>test</unit>
</zend-config>

EOD;

}
