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
 * @package     Glitch_Application
 * @subpackage  Resource
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Resource for initializing the plugin loader
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_PluginLoader extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Name of the plugin loader class
     *
     * This name may be overridden by extending classes in order to provide
     * a custom class. Set the new value in the child init().
     *
     * @var string
     */
    protected $_className = 'Zend_Loader_PluginLoader';

    /**
     * Name of the plugin cache file
     *
     * This name may be overridden by extending classes in order to provide
     * a custom file name. Set the new value in the child init().
     *
     * @var string
     */
    protected $_filename = 'PluginLoaderCache.php';

    /**
     * Mode of the plugin cache file
     *
     * This mode may be overridden by extending classes in order to provide
     * a custom mode. Set the new value in the child init().
     *
     * @var int
     */
    protected $_filemode = 0666;

    /**
     * Initializes this resource
     *
     * @return void
     */
    public function init()
    {
        $this->_setCache();
    }

    /**
     * Sets the plugin loader cache
     *
     * @return void
     */
    protected function _setCache()
    {
        $options = $this->getOptions();

        // Disable cache? If not defined, cache will be active
        if (isset($options['cache']['active']) && !$options['cache']['active'])
        {
            // Explicitly pass null to disable cache, in case it was enabled before
            call_user_func(array($this->_className, 'setIncludeFileCache'), null);
            return;
        }

        // Plugin cache is always stored in dedicated directory
        $cacheFile = GLITCH_CACHES_PATH . DIRECTORY_SEPARATOR . $this->_filename;

        // Set appropriate rights, to allow access for both CLI and HTTP users/groups.
        // Must be done early, because we can't hook into setIncludeFileCache() to do it later on.
        if (!file_exists($cacheFile))
        {
            if (false === file_put_contents($cacheFile, '<?php' . PHP_EOL) ||
                false === chmod($cacheFile, $this->_filemode))
            {
                $this->_bootstrap->bootstrap('Log');
                $log = $this->_bootstrap->getResource('Log');
                $log->warn('Failed to create or chmod plugin cache file "' . $this->_filename . '"');

                return;
            }
        }

        include_once $cacheFile;

        // Use trick to call static method on a variable class name
        call_user_func(array($this->_className, 'setIncludeFileCache'), $cacheFile);
    }
}