<?php
/**
 * Glitch
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
 * @version     $Id$
 */

/**
 * Concrete class for handling view scripts
 *
 * This class overrides certain default methods in order to generate view paths
 * that comply with the custom autoloading structure: view scripts and filters are
 * loaded from a directory structure identical to their class names, e.g.
 * class "Default_View_Helper_Test" --> file "Default/View/Helper/Test.php"
 *
 * @category    Glitch
 * @package     Glitch
 */
class Glitch_View extends Zend_View
{
    /**#@+
     * View path directories
     *
     * @var string
     */
    const PATH_VIEW = 'View';
    const PATH_SCRIPT = 'Script';
    const PATH_HELPER = 'Helper';
    const PATH_FILTER = 'Filter';
    /**#@-*/

    /**
     * Helper for fixing a directory path
     *
     * @param string $path
     * @return string
     */
    protected static function _fixPath($path)
    {
        $path  = rtrim($path, '/');
        $path  = rtrim($path, '\\');
        $path .= DIRECTORY_SEPARATOR;

        // Make path absolute so ZF doesn't need to resolve it
        $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');

        if ((!$isWindows && substr($path, 0, 1) != DIRECTORY_SEPARATOR) ||
             ($isWindows && !preg_match('~^[a-z]:~i', $path)))
        {
            // @todo Can we use modules path all the time?
            $path = GLITCH_MODULES_PATH . DIRECTORY_SEPARATOR . $path;
        }

        return $path;
    }

    /**
     * Given a base path, sets the script, helper, and filter paths relative to it
     *
     * Assumes a directory structure of:
     * <code>
     * basePath/
     *     Script/
     *     Helper/
     *     Filter/
     * </code>
     *
     * @param  string $path
     * @param  string $classPrefix Prefix to use for helper and filter paths
     * @return Glitch_View
     */
    public function setBasePath($path, $classPrefix = 'Zend_View')
    {
        $path = self::_fixPath($path);
        $classPrefix = rtrim($classPrefix, '_') . '_';

        $this->setScriptPath($path . Glitch_View::PATH_SCRIPT);
        $this->setHelperPath($path . Glitch_View::PATH_HELPER, $classPrefix . Glitch_View::PATH_HELPER);
        $this->setFilterPath($path . Glitch_View::PATH_FILTER, $classPrefix . Glitch_View::PATH_FILTER);

        return $this;
    }

    /**
     * Given a base path, add script, helper, and filter paths relative to it
     *
     * Assumes a directory structure of:
     * <code>
     * basePath/
     *     Script/
     *     Helper/
     *     Filter/
     * </code>
     *
     * @param  string $path
     * @param  string $classPrefix Prefix to use for helper and filter paths
     * @return Glitch_View
     */
    public function addBasePath($path, $classPrefix = 'Zend_View')
    {
        $path = self::_fixPath($path);
        $classPrefix = rtrim($classPrefix, '_') . '_';

        $this->addScriptPath($path . Glitch_View::PATH_SCRIPT);
        $this->addHelperPath($path . Glitch_View::PATH_HELPER, $classPrefix . Glitch_View::PATH_HELPER);
        $this->addFilterPath($path . Glitch_View::PATH_FILTER, $classPrefix . Glitch_View::PATH_FILTER);

        return $this;
    }

    protected function _run()
    {
        $_ = function($string) {
            $options = func_get_args();
            array_shift($options);

            return Zend_Layout::getMvcInstance()
                        ->getView()
                            ->getHelper('translate')->translate($string, $options);
        };

        if ($this->useStreamWrapper()) {
            include 'zend.view://' . func_get_arg(0);
        } else {
            include func_get_arg(0);
        }
    }
}
