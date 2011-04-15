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
 * Storage class for managing global data
 *
 * This class implements convenience methods to allow for easy, consistent access to common objects.
 *
 * It uses named methods (e.g. getConfig()) rather than hash look-ups (e.g. get('config')) because:
 * (1) Named methods allow for additional implementation
 * (2) Named methods do not depend upon a specific, hard-coded key ('config')
 *
 * @category    Glitch
 * @package     Glitch
 */
class Glitch_Registry extends Zend_Registry
{
    /**#@+
     * Names of the registry keys
     *
     * @var string
     */
    const KEY_CONFIG = 'config';
    const KEY_SETTINGS = 'settings';
    const KEY_DB = 'db';
    const KEY_ENCODING = 'encoding';
    const KEY_LOCALE = 'Zend_Locale'; // Special case, required by ZF
    const KEY_TRANSLATE = 'Zend_Translate'; // Special case, required by ZF
    const KEY_LOG = 'log';
    /**#@-*/

    /**
     * Registry for the various registered caches on the Cache Manager
     *
     * @var array
     */
    private static $_managedCaches = array();

    /**
     * Gets the configuration
     *
     * @return Zend_Config
     */
    public static function getConfig()
    {
    	return self::get(self::KEY_CONFIG);
    }

    /**
     * Sets the configuration
     *
     * @param Zend_Config $config
     * @return void
     */
    public static function setConfig(Zend_Config $config)
    {
        self::set(self::KEY_CONFIG, $config);
    }

    /**
     * Gets the application settings
     *
     * @return Zend_Config
     */
    public static function getSettings()
    {
        return self::get(self::KEY_SETTINGS);
    }

    /**
     * Sets the application settings
     *
     * @param Zend_Config $config
     * @return void
     */
    public static function setSettings(Zend_Config $config)
    {
        self::set(self::KEY_SETTINGS, $config);
    }

    /**
     * Gets the database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public static function getDb()
    {
        return self::get(self::KEY_DB);
    }

    /**
     * Sets the database adapter
     *
     * @param Zend_Db_Adapter_Abstract $db
     * @return void
     */
    public static function setDb(Zend_Db_Adapter_Abstract $db)
    {
        self::set(self::KEY_DB, $db);
    }

    /**
     * Gets the encoding
     *
     * @return string
     */
    public static function getEncoding()
    {
        return self::get(self::KEY_ENCODING);
    }

    /**
     * Sets the encoding
     *
     * @param string $encoding
     * @return void
     */
    public static function setEncoding($encoding)
    {
        self::set(self::KEY_ENCODING, $encoding);
    }

    /**
     * Gets the locale
     *
     * @return Zend_Locale
     */
    public static function getLocale()
    {
        return self::get(self::KEY_LOCALE);
    }

    /**
     * Sets the locale
     *
     * @param Zend_Locale $locale
     * @return void
     */
    public static function setLocale(Zend_Locale $locale)
    {
        self::set(self::KEY_LOCALE, $locale);
    }

    /**
     * Gets the logger
     *
     * @return Zend_Log
     */
    public static function getLog()
    {
        return self::get(self::KEY_LOG);
    }

    /**
     * Sets the logger
     *
     * @param Zend_Log $log
     * @return void
     */
    public static function setLog(Zend_Log $log)
    {
        self::set(self::KEY_LOG, $log);
    }

    /**
     * Gets the translator
     *
     * @return Zend_Translate
     */
    public static function getTranslate()
    {
        return self::get(self::KEY_TRANSLATE);
    }

    /**
     * Sets the translator
     *
     * @param Zend_Translate $translate
     * @return void
     */
    public static function setTranslate(Zend_Translate $translate)
    {
        self::set(self::KEY_TRANSLATE, $translate);
    }

    /**
     * Get a cache type from the cache manager
     *
     * Get a cache type via the Cache Manager instance or instantiate the object if not
     * exists. Attempts to load from bootstrap if available.
     *
     * @param string
     * @return Zend_Cache_Core
     * @throws InvalidArgumentException when incorrect type is provided
     */
    public static function getCache($type)
    {
    	if (is_string($type) && !array_key_exists($type, self::$_managedCaches))
    	{
    		    		$front = Zend_Controller_Front::getInstance();
	        if ($front->getParam('bootstrap') && $front->getParam('bootstrap')->getResource('CacheManager'))
	        {
	            $manager = $front->getParam('bootstrap')
	                             ->getResource('CacheManager');
	        }
	        else
	        {
	        	$manager = new Zend_Cache_Manager();
	        }

	        if (!$manager->hasCache($type))
	        {
	            throw new InvalidArgumentException('Cache of type '. $type . ' does not exist!');
	        }
	        self::$_managedCaches[$type] = $manager->getCache($type);
    	}
        return self::$_managedCaches[$type];
    }
}