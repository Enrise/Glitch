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
 * @version     $Id: index.php 7102 2010-09-25 14:00:10Z sdvalk $
 */

define('APP_NAME', 'glitch3_web_application'); //please don't use spaces

require_once '../application/Init.php';

// Performance: keep this path as short as possible
set_include_path(GLITCH_LIB_PATH . PATH_SEPARATOR . GLITCH_MODULES_PATH);

// Performance: utilize autoloading, omit require_once() calls
require_once GLITCH_LIB_PATH . '/Glitch/Loader/Autoloader.php';
new Glitch_Loader_Autoloader();

// Note: This must be called PRIOR to any calls to Zend_Controller_Front!
Glitch_Controller_Front::getInstance();

// Initialize the application
$application = new Zend_Application(GLITCH_APP_ENV, Glitch_Config_Ini::getConfig());

$application->bootstrap();
$application->run();
