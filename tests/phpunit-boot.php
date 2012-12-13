<?php

define('GLITCH_APP_ENV', 'testing');

$glitchPath = realpath(dirname(__FILE__)) . '/..';
set_include_path(
    $glitchPath . '/library' . PATH_SEPARATOR .
        $glitchPath . '/dev/submodules/ZF2' . PATH_SEPARATOR .
    get_include_path()
);

// PHPUnit dependencies
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

require_once 'Glitch/Loader/Autoloader.php';
new Glitch_Loader_Autoloader();

// Make sure that Glitch_Controller_Front is used instead of Zend_*
Glitch_Controller_Front::getInstance();

