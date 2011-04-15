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
 * Resource for setting translation options
 *
 * This resource allows just one adapter: an INI file. INI files have excellent performance
 * (also when compared to gettext files) and are easy to maintain. In addition, make sure
 * the cachemanager is configured properly for even better performance.
 *
 * Note that a primary feature of Zend_Translate is NOT used: scanning of translation files.
 * Although it's tempting to use it, directory scanning kills performance. Instead,
 * this resource looks for (1) a shared translation file (e.g. "nl_NL.ini") and, on request,
 * (2) a module-specific translation file (e.g. "nl_NL.Default.ini").
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Translate extends Zend_Application_Resource_Translate implements Glitch_Application_Resource_ModuleInterface
{
    /**
     * Adapter name
     *
     * @var string
     */
    protected $_adapterName = Zend_Translate::AN_INI;

    /**
     * Adapter file extension (dot omitted!)
     *
     * @var string
     */
    protected $_adapterFileExtension = 'ini';

    /**
     * Sets the translation cache
     *
     * @return void
     */
    protected function _setCache()
    {
        $options = $this->getOptions();

        // Disable cache? If not defined, cache will be active
        if (isset($options['cache']['active']) && !$options['cache']['active'])
        {
            // Explicitly remove cache, in case it was set before
            Zend_Translate::removeCache();
            return;
        }

        // Get the cache using the config settings as input
        $this->_bootstrap->bootstrap('CacheManager');
        $manager = $this->_bootstrap->getResource('CacheManager');
        $cache = $manager->getCache('translate');

        // Write caching errors to log file (if activated in the config)
        $this->_bootstrap->bootstrap('Log');
        $logger = $this->_bootstrap->getResource('Log');
        $cache->setOption('logger', $logger);

        Zend_Translate::setCache($cache);
    }

    /**
     * Retrieves the translate object
     *
     * @return Zend_Translate
     */
    public function getTranslate()
    {
        if (null === $this->_translate)
        {
            $options = $this->getOptions();

            // First init cache, then create the translator
            $this->_setCache();

            // Ensure locale is set: required by translator. There's no need to
            // pass this locale to translate, though: will be done automatically.
            $this->_bootstrap->bootstrap('Locale');
            $locale = $this->_bootstrap->getResource('Locale');

            // Load the file with shared, module-independant translations.
            // Performance: use absolute path, so that ZF doesn't need to resolve it
            // Filepath format: {locale}/{locale}.ini, e.g. "nl_NL/nl_NL.ini".
            $filename = sprintf('%s%s%s%s%s.%s',
                    GLITCH_LANGUAGES_PATH,
                    DIRECTORY_SEPARATOR,
                    $locale->toString(),
                    DIRECTORY_SEPARATOR,
                    $locale->toString(),
                    $this->_adapterFileExtension
                );

            // Config may contain additional translate options
            $params = (isset($options['options'])) ? $options['options'] : array();

            // Auto-set the logger to which notices and messages are written to
            $this->_bootstrap->bootstrap('Log');
            $params['log'] = $this->_bootstrap->getResource('Log');

            $this->_translate = new Zend_Translate($this->_adapterName, $filename, null, $params);

            // Allow application-wide access; e.g. Zend_Form uses this.
            // Use the registry to change the locale at some point in your
            // application, i.e. after the user has switched language:
            // Glitch_Registry::getTranslate()->setLocale('en_GB')
            Glitch_Registry::setTranslate($this->_translate);
        }
        return $this->_translate;
    }

    /**
     * Sets module-specific options
     *
     * This method is called automatically by the Modules controller plugin
     *
     * @param string $module
     * @return void
     */
    public function setModuleOptions($module)
    {
        // Format $module if unformatted, e.g. "default" --> "Default"
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        $module = $dispatcher->formatModuleName($module);

        $options = $this->getOptions();

        // Grab the modules with translations, if any
        $modules = array();
        if (isset($options['modules']))
        {
            $modules = (array) $options['modules'];
        }

        // Performance: don't attempt to load module translation unless explicitly defined
        if (!in_array($module, $modules))
        {
            return;
        }

        $this->_bootstrap->bootstrap('Locale');
        $locale = $this->_bootstrap->getResource('Locale');

        // Performance: use absolute path, so that ZF doesn't need to resolve it.
        // Filepath format: {locale}/{module}.ini, e.g. "nl_NL/Default.ini".
        // Don't test whether the file exists - Zend_Translate takes care of that
        $filename = sprintf('%s%s%s%s%s.%s',
            GLITCH_LANGUAGES_PATH,
            DIRECTORY_SEPARATOR,
            $locale->toString(),
            DIRECTORY_SEPARATOR,
            $module,
            $this->_adapterFileExtension
        );

        // Add the module-specific file to the existing translations.
        // Be aware: identical messages will be overwritten by this new translation.
        $this->getTranslate()->addTranslation($filename);
    }
}