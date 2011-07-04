<?php

class Glitch_Application_Resource_RouterTest
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
                array('locations'   => array('name' => 'location')))));

    public function testCliRouterIsPickedWhenCorrectEnvsEnabled()
    {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();

        Glitch_Console_Getopt::reset(); // Better safe than sorry
        $router = new GlitchTest_Application_Resource_RouterEnv();
        $router->setPhpSapi('cli');
        $router->setApplicationEnvironment('production');
        $router->setBootstrap($bootstrap);

        $this->assertInstanceOf('Glitch_Controller_Router_Cli', $router->getRouter());
    }

    public function testExceptionIsThrownOnMissingRestMappings()
    {
        $app = new Zend_Application('testing', $this->_appConfig);
        $bootstrap = new Glitch_Application_Bootstrap_Bootstrap($app);
        $bootstrap->bootstrap();

        Glitch_Console_Getopt::reset(); // Better safe than sorry
        $router = new Glitch_Application_Resource_Router();
        $router->setBootstrap($bootstrap);

        try {
            $router->getRestMappings();
            $this->fail('Exception expected');
        } catch (\RuntimeException $e) {
            $this->assertEquals(
            	'The rest mappings were tried to retrieve but have not been set',
                $e->getMessage()
            );
        }
    }

}

class GlitchTest_Application_Resource_RouterEnv extends Glitch_Application_Resource_Router
{
    protected $_sapi = PHP_SAPI;

    protected $_appEnv = GLITCH_APP_ENV;

    public function setPhpSapi($sapi)
    {
        $this->_sapi = $sapi;
    }

    public function setApplicationEnvironment($appEnv)
    {
        $this->_appEnv = $appEnv;
    }

    protected function _getPhpSapi()
    {
        return $this->_sapi;
    }

    protected function _getApplicationEnvironment()
    {
        return $this->_appEnv;
    }
}
