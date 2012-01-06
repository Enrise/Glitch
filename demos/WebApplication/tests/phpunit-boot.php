<?php

// We test in environment "testing"
putenv('GLITCH_APP_ENV=testing');

define('APP_NAME', 'MainflowApiUnitTest');
require_once '../application/Init.php';

// Performance: keep this path as short as possible
set_include_path( GLITCH_LIB_PATH . PATH_SEPARATOR . GLITCH_MODULES_PATH . PATH_SEPARATOR . GLITCH_APP_PATH . PATH_SEPARATOR . "/usr/local/zend/share/pear/" . PATH_SEPARATOR . "/usr/share/php");

// PHPUnit dependencies
//require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

// Performance: utilize autoloading, omit require_once() calls
require_once GLITCH_LIB_PATH . '/Glitch/Loader/Autoloader.php';
new Glitch_Loader_Autoloader();

// Make sure that Glitch_Controller_Front is used instead of Zend_*
Glitch_Controller_Front::getInstance();

include_once GLITCH_APP_PATH . "/../application/Bootstrap.php";

// Initialize the application
$application = new Zend_Application(GLITCH_APP_ENV, Glitch_Config_Ini::getConfig());

// Bootstrap all resource methods and plugins
$application->bootstrap();
