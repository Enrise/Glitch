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
class Glitch_Application_Resource_Translate
    extends Zend_Application_Resource_Translate
//    implements Glitch_Application_Resource_ModuleInterface
{

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

            $this->_bootstrap->bootstrap('Log');
            if (null !== ($log = $this->_bootstrap->getResource('Log'))) {
                $options['log'] = $this->_bootstrap->getResource('Log');
            }

            if (($cache = $this->_getCache($options)) != null) {
                $options['cache'] = $cache;
            } else {
                unset($options['cache']);
            }

            if ($options['modular']) {
                $iterator = new FilesystemIterator($options['dataDir'], FilesystemIterator::SKIP_DOTS);
                foreach ($iterator as $item) {
                    // Not all dirs in data/ are modules (thhink of data/jenkins)
                    // so therefore we check if the translation dir actually exists
                    $dir = $item . '/' . $options['content'];
                    if ($item->isDir() && file_exists($dir))
                    {
                        $this->_addDir($dir, $options);
                    }
                }

            } else {
                $this->_addDir($options['content'], $options);
            }

            $this->_options = $options;
        }

        $this->_saveInstance($this->_translate, $options);

        return $this->_translate;
    }

    protected function _addDir($dir, array $options = array())
    {
        $options['scan'] = Zend_Translate::LOCALE_DIRECTORY;
        $options['content'] = $dir;

        if (null === $this->_translate) {
            if (!isset($options['adapter'])) {
                throw new Glitch_Application_Exception_RuntimeException(
                    'A translation adapter must be specified but wasn\'t'
                );
            }

            $adapterName = 'Zend_Translate_Adapter_' . ucfirst($options['adapter']);
            $this->_translate = new $adapterName($options);
        } else {
            $this->_translate->addTranslation($options);
        }
    }

    protected function _saveInstance($translate, array $options = array())
    {
        $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
                 ? $options['registry_key']
                 : self::DEFAULT_REGISTRY_KEY;
        Zend_Registry::set($key, $translate);
    }

    protected function _getCache(array $options = array())
    {
        if (isset($options['cache']['active']) && !$options['cache']['active'])
        {
            Zend_Translate::removeCache();
            return false;
        } else {
            $this->_bootstrap->bootstrap('CacheManager');
            $manager = $this->_bootstrap->getResource('CacheManager');
            $cache = $manager->getCache('translate');

            if (!$cache) {
                throw new Glitch_Application_Exception_RuntimeException(
                    'Translation caching was enabled but no caching object
                     identified by "translate" could be retrieved from the
                     cachemanager'
                );
            }

            if(isset($options['log'])) {
                $cache->setOption('logger', $options['log']);
            }

            return $cache;
        }
    }

}
