<?php
// Define Name of the application if not already done so.
if (! defined('APP_NAME')) {
    define('APP_NAME', 'DefaultApplication');
}

// Define the application environment
if(!defined('GLITCH_APP_ENV')) {
    $environment = getenv('GLITCH_APP_ENV');
    if (!in_array($environment, array('development', 'testing', 'qa', 'acceptance', 'production')))
    {
        // Don't bootstrap an already crippled application
        throw new Exception('Unknown environment mode: ' . $environment);
    }

    define('GLITCH_APP_ENV', $environment);
}

/**#@+
 * Common directory paths
 *
 * @var string
 */
if (! defined('APP_ROOT')) {
    define('APP_ROOT', realpath(dirname(__FILE__) . '/../'));
}
define('GLITCH_PUBLIC_PATH', APP_ROOT . '/public');

define('GLITCH_LIB_PATH', APP_ROOT . '/library');
define('GLITCH_APP_PATH', APP_ROOT . '/application');
define('GLITCH_DATA_PATH', APP_ROOT . '/data');
define('GLITCH_MODULES_PATH', GLITCH_APP_PATH . '/modules');
define('GLITCH_CONFIGS_PATH', GLITCH_APP_PATH . '/configs');
define('GLITCH_PIDS_PATH', APP_ROOT . '/var/pids');
/**#@-*/