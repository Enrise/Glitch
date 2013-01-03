<?php

define('GLITCH_APP_ENV', 'testing');

$glitchPath = realpath(dirname(__FILE__)) . '/..';
set_include_path(
    $glitchPath . '/library' . PATH_SEPARATOR .
    $glitchPath . '/dev/submodule/ZF2/library' . PATH_SEPARATOR .
    $glitchPath . '/dev/submodule/Phpunit-3.6' . PATH_SEPARATOR .
    get_include_path()
);

require_once 'Glitch/Loader/Autoloader.php';
new Glitch_Loader_Autoloader();

// Make sure that Glitch_Controller_Front is used instead of Zend_*
Glitch_Controller_Front::getInstance();

