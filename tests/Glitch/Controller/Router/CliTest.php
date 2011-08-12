<?php

require_once dirname(__FILE__) . '/../Request/_data/RestMock.php';

class Glitch_Controller_Router_CliTest
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

    public function setUp()
    {
        if(ini_get('register_argc_argv') == false) {
            $this->markTestSkipped(
                    'Cannot Test Zend_Console_Getopt without '
                  . '\'register_argc_argv\' ini option true.'
            );
        }

        $_SERVER['argv'] = array('getopttest');
    }

   public function testExceptionIsThrownIfNoGetOptInstanceSet()
   {
        Glitch_Console_Getopt::reset(); // Better safe than sorry

        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();

        $request = new Glitch_Controller_Request_RestMock(
                        'http://example.net/decision/location/5/defects',
                    $bootstrap);

        $router = new Glitch_Controller_Router_Cli();
        $this->assertEquals($router::CONSOLE_GETOPT_KEY, 'Glitch_Controller_Router_Cli');

        try {
            $router->route($request);
            $this->fail('Exception expected');
        } catch(Glitch_Console_Exception_RuntimeException $e) {
            $this->assertTrue(true);
        }
    }

    public function testExceptionIsThrownOnNonPresentRequestParam()
    {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();

        $request = new Glitch_Controller_Request_RestMock(
                        'http://example.net/decision/location/5/defects',
                    $bootstrap);

        $console = new Glitch_Console_Getopt(
            array(
                'request|r=s' => 'Sets the request to process in format module.controller.action',
                'params|p=s' => 'Adds parameters to the request in format key=value[&key=value]'),
            array('--params=noRequest')
        );

        $router = new Glitch_Controller_Router_Cli();
        $console->saveInstance($router::CONSOLE_GETOPT_KEY);

        try {
            $router->route($request);
            $this->fail('Exception expected');
        } catch(Glitch_Controller_Router_Exception_InvalidArgumentException $e) {
            $this->assertEquals(
                $e->getMessage(),
                'No Request String found in Glitch_Console_GetOpt'
            );
        }

        Glitch_Console_Getopt::reset();
    }

    public function testExceptionIsThrownInvalidRequestParam()
    {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();

        $request = new Glitch_Controller_Request_RestMock(
                        'http://example.net/decision/location/5/defects',
                    $bootstrap);
        $router = new Glitch_Controller_Router_Cli();

        $reqStrings = array(
            '--request=foobar',
            '--request=foo.bar',
            '--request=foo.bar.john.doe',
            '--request=..',
            '--request=foo..bar');

        foreach($reqStrings as $reqString) {
            $console = new Glitch_Console_Getopt(
                array(
                    'request|r=s' => 'Sets the request to process in format module.controller.action',
                    'params|p=s' => 'Adds parameters to the request in format key=value[&key=value]'),
                array($reqString)
            );

            $console->saveInstance($router::CONSOLE_GETOPT_KEY);

            try {
                $router->route($request);
                $this->fail('Exception expected');
            } catch(Glitch_Controller_Router_Exception_InvalidArgumentException $e) {
                $this->assertEquals(
                    $e->getMessage(),
                    'Request is not in format module.controller.action'
                );
            }
        }

        Glitch_Console_Getopt::reset();
    }

    public function testParsingOfInput()
    {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();

        $request = new Glitch_Controller_Request_RestMock(
                        'http://example.net/decision/location/5/defects',
                    $bootstrap);

        $usecases = array(array('reqstring' => array('--request=module.controller.action'),
                                'module' => 'module',
                                'controller' => 'controller',
                                'action' => 'action',
                                'params' => array()),
                          array('reqstring' => array('--request=www.enrise.com',
                                                       '--params=euro=€'),
                                'module' => 'www',
                                'controller' => 'enrise',
                                'action' => 'com',
                                'params' => array('euro' => '€')));

        foreach($usecases as $usecase) {
            $console = new Glitch_Console_Getopt(
                array(
                    'request|r=s' => 'Sets the request to process in format module.controller.action',
                    'params|p=s' => 'Adds parameters to the request in format key=value[&key=value]'),
                $usecase['reqstring']
            );

            $router = new Glitch_Controller_Router_Cli();
            $console->saveInstance($router::CONSOLE_GETOPT_KEY);

            $router->route($request);
            $this->assertEquals($usecase['module'], $request->getModuleName());
            $this->assertEquals($usecase['controller'], $request->getControllerName());
            $this->assertEquals($usecase['action'], $request->getActionName());
            $this->assertEquals($usecase['params'], $request->getParams());

            Glitch_Console_Getopt::reset();
        }
    }

}
