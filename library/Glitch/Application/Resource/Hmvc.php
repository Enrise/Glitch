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
 * Resource for setting HMVC options
 *
 * To be able to use the HMVC structure the following is possible from config
 *
 * [production]
 *
 * resources.frontController.controllerDirectory.rest = GLITCH_MODULES_PATH "/Rest/Controller"
 *
 * ;if HMVC should be enalbed at all
 * resources.hmvc.active = true
 * ;should a hierarchical url be followed?
 * resources.hmvc.redispatch = true
 * ;Allow to load a custom plugin class
 * resources.hmvc.pluginClass = "Example_Controller_Plugin_Hmvc"
 * ;Register the active modules to which HMVC should respond
 * resources.hmvc.modules[] = "rest"
 *
 * [staging : production]
 *
 * [testing : staging]
 *
 * [development : testing]
 *
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Hmvc extends Zend_Application_Resource_ResourceAbstract implements Glitch_Application_Resource_ModuleInterface
{
    /**
     * Default class this resource will use for the HMVC controller plugin.
     * This is private as we do not allow the default to be overwritten.
     * That can be done by specifying the plugin class you wish to use in the config by using the "pluginClass" key.
     *
     * @var string
     */
    private $_defaultPluginClass = 'Glitch_Controller_Plugin_Hmvc';

    /**
     * Initialize the HMVC if active
     *
     * @return null|Glitch_Controller_Plugin_Hmvc
     */
    public function init()
    {
        if (array_key_exists('active', $this->_options) && $this->_options['active'])
        {
            return $this->getHmvc();
        }
    }

    /**
     * Get the plugin name to use
     * Defaults back to the defaultPluginClass if none is available from config
     *
     * @return string
     */
    public function getPluginClass()
    {
        if (isset($this->_options->pluginClasss) && is_string($this->_options->pluginClass))
        {
            return $this->_options->pluginClass;
        }
        return $this->_defaultPluginClass;
    }

    /**
     * Init the HMVC controller plugin and set its modules
     *
     * @return Glitch_Controller_Plugin_Hmvc
     */
    public function getHmvc()
    {
        // push the profiler on the plugin stack to time the dispatch process
        $front = $this->_bootstrap->getResource('FrontController');
        $class = $this->getPluginClass();
        $plugin = new $class;
        if (!$plugin instanceof $this->_defaultPluginClass)
        {
            throw new Zend_Application_Resource_Exception('Plugin needs to be an instanceof ' . $this->_defaultPluginClass);
        }
        $modules = array();
        if (array_key_exists('modules', $this->_options) && is_array($this->_options['modules']))
        {
            $modules = $this->_options['modules'];
        }
        $plugin->setActiveModules($modules);
        $front->registerPlugin($plugin);
        return $plugin;
    }

    /**
     * Does nothing..
     * @param $options
     */
    public function setModuleOptions($options){}
}