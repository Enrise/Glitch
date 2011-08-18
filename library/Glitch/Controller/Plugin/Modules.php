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
 * @package     Glitch_Controller
 * @subpackage  Plugin
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Early-running plugin for setting module-specific options on application resources
 *
 * This plugin is required since application resources are unaware of the requested module:
 * resources are bootstrappers, for getting ready to execute. Note that this plugin is *not*
 * a full-fledged solution to ZF's lack of module-based configuration:
 * http://weierophinney.net/matthew/archives/234-Module-Bootstraps-in-Zend-Framework-Dos-and-Donts.html
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Plugin
 */
class Glitch_Controller_Plugin_Modules extends Zend_Controller_Plugin_Abstract
{
    /**
     * Bootstrap object
     *
     * @var Zend_Application_Bootstrap_BootstrapAbstract
     */
    protected $_bootstrap = null;

    /**
     * Retrieves the bootstrapper from the front controller
     *
     * @return Zend_Application_Bootstrap_BootstrapAbstract
     * @throws Glitch_Controller_Exception
     */
    protected function _getBootstrap()
    {
        if (null === $this->_bootstrap)
        {
            $this->_bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            if (!$this->_bootstrap instanceof Zend_Application_Bootstrap_BootstrapAbstract)
            {
                throw new Glitch_Controller_Exception('Class is not a valid bootstrap instance');
            }
        }
        return $this->_bootstrap;
    }

    /**
     * Called after Zend_Controller_Router exits
     *
     * This method iterates over the available plugin resources and looks for
     * instances of Glitch_Application_Resource_ModuleInterface. If it is, the special
     * method 'setModuleOptions' is invoked, passing the current module name. This
     * allows the method to initialize resources (e.g. layouts and sessions) that
     * are bound to a module.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        // Local copy, for efficiency
        $bootstrap = $this->_getBootstrap();

        // Retrieve and normalize the registered plugins
        $plugins = $bootstrap->getPluginResourceNames();
        $plugins = array_unique(array_map('strtolower', $plugins));

        foreach ($plugins as $plugin)
        {
            // Set module-specific options in resource?
            $resource = $bootstrap->getPluginResource($plugin);
            if ($resource instanceof Glitch_Application_Resource_ModuleInterface)
            {
                $resource->setModuleOptions($request->getModuleName());
            }
        }
    }
}