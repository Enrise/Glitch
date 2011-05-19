<?php
/**
 * Helper file for unit testing. Based on ZF's TestHelper.php
 *
 * This source file is proprietary and protected by international
 * copyright and trade secret laws. No part of this source file may
 * be reproduced, copied, adapted, modified, distributed, transferred,
 * translated, disclosed, displayed or otherwise used by anyone in any
 * form or by any means without the express written authorization of
 * 4worx software innovators BV (www.4worx.com)
 *
 * @category    Glitch
 * @package     Glitch
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id: TestHelper.php 6352 2010-08-16 15:02:45Z tpater $
 */

// Error reporting level to which IAP code must comply
error_reporting(E_ALL | E_STRICT);

// Determine the root, library, and tests directories of the application
$root = dirname(dirname(__FILE__));
$applicationpath = $root . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR. 'modules';
$libraryPath = $root . DIRECTORY_SEPARATOR . 'library';
$testsPath = $root . DIRECTORY_SEPARATOR . 'tests';
$applicationTestsPath = $root . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR. 'modules';

// Prepend the directories to the include_path.
// This allows the tests to run out of the box and helps prevent loading
// other copies of the framework code and tests that would supersede this copy.
$path = array($applicationpath, $libraryPath, $testsPath, $applicationTestsPath, get_include_path());
set_include_path(implode(PATH_SEPARATOR, $path));

// Unset global variables that are no longer needed
unset($root, $libraryPath, $testsPath, $path);

// ZF operates without require_once's, so use an autoloader instead
function __autoload($class)
{
    require_once str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
}

// PHPUnit dependencies
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';