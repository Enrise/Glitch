<?php
/**
 * Main entry point for the HTTP interface
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
 * @copyright   2010, 4worx
 * @version     $Id: index.php 8407 2010-11-01 16:57:41Z jthijssen $
 */

/**#@+
 * Common directory paths
 *
 * @var string
 */
define('GLITCH_PUBLIC_PATH', dirname(__FILE__));
define('GLITCH_LIB_PATH', dirname(GLITCH_PUBLIC_PATH) . '/library');
define('GLITCH_APP_PATH', dirname(GLITCH_PUBLIC_PATH) . '/application');
define('GLITCH_DATA_PATH', dirname(GLITCH_PUBLIC_PATH) . '/data');
define('GLITCH_MODULES_PATH', GLITCH_APP_PATH . '/modules');
define('GLITCH_CACHES_PATH', GLITCH_DATA_PATH . '/caches');
define('GLITCH_CONFIGS_PATH', GLITCH_APP_PATH . '/configs');
define('GLITCH_LOGS_PATH', GLITCH_DATA_PATH . '/logs');
define('GLITCH_PIDS_PATH', GLITCH_DATA_PATH . '/pids');
define('GLITCH_LANGUAGES_PATH', GLITCH_DATA_PATH . '/locales');
/**#@-*/

// Performance: keep this path as short as possible
set_include_path(get_include_path() . PATH_SEPARATOR . GLITCH_LIB_PATH . PATH_SEPARATOR . GLITCH_MODULES_PATH . PATH_SEPARATOR . GLITCH_APP_PATH . PATH_SEPARATOR . "/usr/local/zend/share/pear/");

// PHPUnit dependencies
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

require_once GLITCH_LIB_PATH.'/OAuth/OAuth.php';


// Define the application environment
$environment = 'testing';
if (!in_array($environment, array('development', 'testing', 'staging', 'production')))
{
    // Don't bootstrap an already crippled application
    throw new Exception('Unknown environment mode: ' . $environment);
}

/**
 * Application environment: development, testing, staging or production
 *
 * @var string
 */
define('GLITCH_APP_ENV', $environment);

// Performance: utilize autoloading, omit require_once() calls
require_once GLITCH_LIB_PATH . '/Glitch/Loader/Autoloader.php';
new Glitch_Loader_Autoloader();

include_once GLITCH_APP_PATH . "/../application/Bootstrap.php";

// Initialize the application
$application = new Zend_Application(GLITCH_APP_ENV, Glitch_Config_Ini::getConfig());

// Bootstrap all resource methods and plugins
$application->bootstrap();
