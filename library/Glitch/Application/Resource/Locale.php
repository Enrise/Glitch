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
 * Resource for initializing the locale
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Locale extends Zend_Application_Resource_Locale
{
    const SESSION_NAME = 'GLITCH_SESSION';

    /**
     * Sets the locale cache
     *
     * @return void
     */
    protected function _setCache()
    {
        $options = $this->getOptions();

        // Disable cache? If not defined, cache will be active
        if (isset($options['cache']['active']) && !$options['cache']['active'])
        {
            // Zend by default creates a cache for Zend_Locale, due to performance
            // considerations. Manually disable cache to override that behaviour.
            Zend_Locale::disableCache(true);
            return;
        }

        // Get the cache using the config settings as input
        $this->_bootstrap->bootstrap('CacheManager');
        $manager = $this->_bootstrap->getResource('CacheManager');
        $cache = $manager->getCache('locale');

        // Write caching errors to log file (if activated in the config)
        $this->_bootstrap->bootstrap('Log');
        $logger = $this->_bootstrap->getResource('Log');
        $cache->setOption('logger', $logger);

        Zend_Locale::setCache($cache);
    }

    /**
     * Retrieves the locale object
     *
     * @return Zend_Locale
     * @throws Glitch_Application_Resource_Exception
     */
    public function getLocale()
    {

        if (null === $this->_locale)
        {
            $options = $this->getOptions();

            // Force these options to be set - don't rely on the defaults!
            if (!isset($options['default']))
            {
                throw new Glitch_Application_Resource_Exception('Locale option "default" not set');
            }

            // First init cache, then create the locale
            $this->_setCache();

            $this->_locale = new Zend_Locale();

            $override = true;
            if (isset($options['allowed'])) {
                if (array_key_exists('autoDetection', $options)) {
                    $autoDetection = (bool) $options['autoDetection'];
                } else {
                    $autoDetection = true;
                }

                $locale = $this->_detectLocale($options['allowed'], $autoDetection);
                $override = ! $locale;
            }

            if ($override) {
                $this->_locale->setLocale($options['default']);
            } else {
                $this->_locale->setLocale($locale);
            }

            Zend_Locale::setDefault($this->_locale);

            // Allow application-wide access; e.g. Zend_Date uses this
            Glitch_Registry::setLocale($this->_locale);

            // Force formatter to use the above registered default locale
            Zend_Locale_Format::setOptions(array('locale' => null));
        }

        return $this->_locale;
    }

    protected function _detectLocale($allowed, $autoDetection = true)
    {
        $session = new Zend_Session_Namespace(self::SESSION_NAME);
        if(isset($session->locale) &&
                 $session->locale != null &&
                 in_array($session->locale, $allowed))
        {
            return $session->locale;
        } elseif ($autoDetection) {
            $envLocale = $this->_locale->getDefault(Zend_Locale::BROWSER, true);

            foreach ($envLocale as $locale => $present) {
                if ($present && in_array($locale, $allowed)) {
                    return $locale;
                }
            }
        }

        return null;
    }
}
