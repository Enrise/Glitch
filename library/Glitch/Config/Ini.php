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
 * @package     Glitch_Config
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Bootstrapper for Zend_Config with caching capabilities
 *
 * @category    Glitch
 * @package     Glitch_Config
 */
class Glitch_Config_Ini
{
    /**
     * Name of the main configuration file
     *
     * @var string
     */
    const FILENAME_APPLICATION = 'application.ini'; // Per Zend_Application convention

    /**
     * Name of the developer-specific configuration file
     *
     * This file is optional; it may not exist
     *
     * @var string
     */
    const FILENAME_USER = 'user.ini';

    /**
     * Lifetime of cache, in seconds
     *
     * @var int|null
     */
    const CACHE_LIFETIME = null; // Valid forever!

    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend, simply override {@link getInstance()}.
     *
     * @staticvar Glitch_Config_Ini
     */
    protected static $_instance = null;

    /**
     * Config object
     *
     * @staticvar Zend_Config
     */
    protected static $_config = null;

    /**
     * Cache object
     *
     * @var Zend_Cache
     */
    protected $_cache = null;

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        $this->_setCache();
    }

    /**
     * Enforce singleton; disallow cloning
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Returns a singleton instance
     *
     * @return Glitch_Config_Ini
     */
    public static function getInstance()
    {
        if (null === self::$_instance)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Sets the configuration cache
     *
     * This is a separate, protected method, thereby allowing
     * extending child classes to override it, in order to provide
     * a different caching mechanism.
     *
     * @return void
     */
    protected function _setCache()
    {
        $frontendOptions = array(
            'lifetime' => self::CACHE_LIFETIME,
            'cached_entity' => __CLASS__
        );
        $backendOptions = array(
            'namespace' => APP_NAME . '_Config'
        );

        $backend = 'BlackHole';
        if(GLITCH_APP_ENV != 'testing' && GLITCH_APP_ENV != 'development') {
            if (function_exists('zend_shm_cache_store'))
            {
                $backend = 'ZendServer_ShMem';
            } else if (extension_loaded('apc')) {
                $backend = 'Apc';
            }
        }

        // Use a shared memory type of caching, because the configuration won't be
        // changed on a regular basis
        $this->_cache = Zend_Cache::factory(
            'Class',
            'Zend_Cache_Backend_' . $backend,
            $frontendOptions,
            $backendOptions,
            false,
            true // Custom backend naming
        );
    }

    /**
     * Loads the configuration
     *
     * First, the main configuration file - "application.ini" - is loaded from
     * the root of the configs directory. Second, all other configuration files,
     * if any, are loaded recursively - except for "user.ini". Third, the
     * developer-specific configuration file - "user.ini" - is loaded. This
     * file overrides any setting in the previously loaded files and is only
     * regarded in development and testing mode.
     *
     * This method must be 'public static' - Zend_Cache_Class requires so.
     * However, users shouldn't call it directly; use getConfig() instead.
     *
     * @param string $section
     * @return Zend_Config
     */
    public static function loadConfig($section)
    {
        // Load the main configuration file
        $configFile = GLITCH_CONFIGS_PATH . DIRECTORY_SEPARATOR . self::FILENAME_APPLICATION;
        $ini = new Zend_Config_Ini($configFile, $section, array('allowModifications' => true));

        // Recursively load all other ini files, if any, but exclude the special cases
        $pattern = '~^(?!'
                 . preg_quote(self::FILENAME_APPLICATION) . '|'
                 . preg_quote(self::FILENAME_USER)
                 . ').+\.ini$~';

        $dirIterator = new RecursiveDirectoryIterator(GLITCH_CONFIGS_PATH, RecursiveDirectoryIterator::KEY_AS_FILENAME);
        $recursiveIterator = new RecursiveIteratorIterator($dirIterator);
        $iterator = new RegexIterator($recursiveIterator, $pattern, RegexIterator::MATCH, RegexIterator::USE_KEY);

        foreach ($iterator as $file)
        {
            $ini->merge(new Zend_Config_Ini($file->getPathname(), $section));
        }

        // Optionally load developer-specific settings, overriding previous settings
        $configFile = GLITCH_CONFIGS_PATH . DIRECTORY_SEPARATOR . self::FILENAME_USER;
        if (file_exists($configFile))
        {
            $ini->merge(new Zend_Config_Ini($configFile, $section));
        }

    if ('testing' != $section) {
            $ini->setReadOnly();
    }

        return $ini;
    }

    /**
     * Gets the configuration
     *
     * This is the preferred method for loading the config. Don't use loadConfig() directly!
     * Be aware: this method is used for bootstrapping the config, e.g. in public/index.php.
     * Once loaded, users ought to call Glitch_Registry::getConfig() or
     * Glitch_Registry::getSettings().
     *
     * @return Zend_Config
     */
    public static function getConfig()
    {
        // Don't call the cached method more than once
        if (null === self::$_config)
        {
            self::$_config = self::getInstance()->_cache->loadConfig(GLITCH_APP_ENV);

            // Allow application-wide access
            Glitch_Registry::setConfig(self::$_config);

            // Store the application settings, if any
            $settings = self::$_config->get('settings');
            if (null !== $settings)
            {
                Glitch_Registry::setSettings($settings);
            }
        }
        return self::$_config;
    }
}
