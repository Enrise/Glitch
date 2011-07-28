#!/usr/bin/php
<?php
/**
 * Main entry point for the CLI interface
 *
 * This source file is proprietary and protected by international
 * copyright and trade secret laws. No part of this source file may
 * be reproduced, copied, adapted, modified, distributed, transferred,
 * translated, disclosed, displayed or otherwise used by anyone in any
 * form or by any means without the express written authorization of
 * 4worx software innovators BV (www.4worx.com)
 *
 * @category    Idm
 * @package     Idm_Startup
 * @author      4worx <info@4worx.com>
 * @copyright   2011, Enrise BV
 */
set_exception_handler(
    function($exception)
    {
        echo $exception->getMessage() . PHP_EOL;

        if (class_exists('Glitch_Registry', false) &&
            Glitch_Registry::isRegistered('log'))
        {
            Glitch_Registry::get('log')->err($exception->getMessage());
        }

        exit(1); // Non-zero value denotes an error
    }
);

// Assuming we're in app root/cli folder
define('APP_ROOT', dirname(__FILE__) . '/..');

require_once APP_ROOT . '/library/Zend/Console/Getopt.php';
require_once APP_ROOT . '/library/Glitch/Console/Getopt.php';

$console = new Glitch_Console_Getopt(array(
	'help|h' => 'Displays usage information',
    'request|r=s' => 'Sets the request to process in format module.controller.action',
    'params|p=s' => 'Adds parameters to the request in format key=value[&key=value]',
    'environment|e=s' => 'Sets the application environment (development, testing, acceptance or production)'
));
$console->parse();
$console->saveInstance('Glitch_Controller_Router_Cli');

if (isset($console->h) || empty($console->r))
{
    throw new Exception('No valid request specified');
}

// Check params, strip them and place them inside $_REQUEST, $_GET and $_POST
if (!empty($console->p)) {
    foreach (explode("&", $console->p) as $var) {
        list($key, $val) = explode("=", $var, 2);
        $_REQUEST[$key] = $val;
        $_POST[$key] = $val;
        $_GET[$key] = $val;
    }
}

// Define the application environment
$environment = (isset($console->e)) ? $console->e : getenv('GLITCH_APP_ENV');
if (!in_array($environment, array('development', 'testing', 'acceptance', 'production')))
{
    throw new Exception('Unknown environment mode: "' . $environment . '"');
}

define('GLITCH_APP_ENV', $environment);
require_once APP_ROOT . '/application/Init.php';

set_include_path(GLITCH_LIB_PATH . PATH_SEPARATOR . GLITCH_MODULES_PATH);
require_once GLITCH_LIB_PATH . '/Glitch/Loader/Autoloader.php';
new Glitch_Loader_Autoloader();

$application = new Zend_Application(GLITCH_APP_ENV, Glitch_Config_Ini::getConfig());

// Bootstrap all resource methods and plugins
$application->bootstrap();
Glitch_Controller_Front::getInstance()->throwExceptions(true);

// Prevent namespace polution: unset global variables that are no longer needed
unset($console, $parts, $params, $environment, $request);

$application->run();

exit(0);
