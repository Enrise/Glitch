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
 * Resource for initializing the database
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Db extends Zend_Application_Resource_Db
{
    /**
     * Initializes this resource
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function init()
    {
        return $this->getDb();
    }

    /**
     * Sets the table metadata cache
     *
     * @return void
     */
    protected function _setCache()
    {
        $options = $this->getOptions();

        // Disable cache? If not defined, cache will be active
        if (isset($options['cache']['active']) && !$options['cache']['active'])
        {
            // Explicitly pass null to deactivate, in case it was enabled before
            Zend_Db_Table_Abstract::setDefaultMetadataCache(null);
            return;
        }

        // Get the cache using the config settings as input
        $this->_bootstrap->bootstrap('CacheManager');
        $manager = $this->_bootstrap->getResource('CacheManager');
        $cache = $manager->getCache('db');

        // Write caching errors to log file (if activated in the config)
        $this->_bootstrap->bootstrap('Log');
        $logger = $this->_bootstrap->getResource('Log');
        $cache->setOption('logger', $logger);

        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
    }

    /**
     * Retrieves the database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDb()
    {
        if (null === $this->_db)
        {
            // Use parent for basic initialization
            parent::init();

            // Has profiler? Attach it to the database adapter
            if ($this->_db->getProfiler()->getEnabled())
            {
                // Check whether this is a HTTP request; if not, don't use Firebug
                $this->_bootstrap->bootstrap('Request');
                $request = $this->_bootstrap->getResource('Request');

                $profiler = ($request instanceof Zend_Controller_Request_Http)
                    ? new Zend_Db_Profiler_Firebug('Database queries')
                    : new Zend_Db_Profiler();

                $profiler->setEnabled(true);
                $this->_db->setProfiler($profiler);
            }

            $this->_setCache();

            // Allow application-wide access
            Glitch_Registry::setDb($this->_db);
        }
        return $this->_db;
    }
}