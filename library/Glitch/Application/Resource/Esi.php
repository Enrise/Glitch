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
 * Resource for initializing the ability to handle ESI calls on a specific url
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Esi extends Zend_Application_Resource_ResourceAbstract
                                      implements Glitch_Application_Resource_ModuleInterface
{
    /**
     * Initializes this resource
     *
     * @return void
     */
    public function init()
    {
        $options = $this->getOptions();
        if (isset($options['active']) && (true === (boolean)$options['active']))
        {
            $this->enableEsi();
        }
    }

    /**
     * Register the custom PHP ESI parser
     *
     * @return void
     */
    public function enableEsi()
    {
        $options = $this->getOptions();

        if (isset($options['headerName']))
        {
            Glitch_View_Helper_Esi::setHeader($options['headerName'], 1);
        }

        // Ensure the front controller is initialized
        $this->_bootstrap->bootstrap('FrontController');
        $front = $this->_bootstrap->getResource('FrontController');

        if (isset($options['phpParser']) && (true === (boolean)$options['phpParser']))
        {
            // Get the cache using the config settings as input
            $this->_bootstrap->bootstrap('CacheManager');
            $manager = $this->_bootstrap->getResource('CacheManager');
            if ($manager->hasCache('esi'))
            {
                $cache = $manager->getCache('esi');
                Glitch_Controller_Plugin_EsiParser::setCache($cache);
            }

            // push the profiler on the plugin stack to time the dispatch process
            $front->registerPlugin(new Glitch_Controller_Plugin_EsiParser());
        }
    }

    /**
     * Enable this specific call as an ESI enabled call thus disabling the layout
     *
     * @param string $module
     * @return void
     */
    public function setModuleOptions($module)
    {
        $options = $this->getOptions();
        $defaultModule = (isset($options['defaultModule'])) ? $options['defaultModule'] : 'snippets';

        if (strtolower($module) == $defaultModule)
        {
            // disable the layout helper, because we are dealing with a snippet call
            $this->getBootstrap()->getPluginResource('layout')->getLayout()->disableLayout();
        }
    }
}