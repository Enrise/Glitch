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
 * Early-running plugin for setting module-specific HMVC structures
 *
 * This plugin will run after the routeShutdown to determine if a module is registered
 * to run as a nested controller structure.
 * Once this is confirmed the plugin will modify the router to follow the url path.
 * Unless you specify that it should go to the last controller by default.
 * This is usefull if you want to check everything at once instead of redispatching through the controllers.
 *
 * For now the request pathInfo is split up by ctype_alpha and ctype_digit logic.
 * Ctype_alpha is used for the controller and ctype_digit is used for the controller param.
 *
 * When following the url's by hierarchy each controller still receives all the split up params.
 * This way you can still play with the complete request.
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Plugin
 * @author      dpapadogiannakis@4worx.com
 */
class Glitch_Controller_Plugin_Hmvc extends Zend_Controller_Plugin_Abstract
{
    /**
     * Array of registred controllers
     *
     * @var array
     */
    protected $_controllers = array();

    /**
     * The position of controllers we are handling
     *
     * @var int
     */
    protected $_pos = 0;

    /**
     * Registered active modules to which HMVC will apply
     *
     * @var array
     */
    protected $_activeModules = array();

    /**
     * Unset all active modules
     *
     * @return Glitch_Controller_Plugin_Hmvc
     */
    public function clearActiveModules()
    {
        $this->_activeModules = array();
        return $this;
    }

    /**
     * Set a whole set of active modules at once
     *
     * @param mixed $modules
     * @return Glitch_Controller_Plugin_Hmvc
     */
    public function setActiveModules($modules)
    {
        if ($modules instanceof Zend_Config)
        {
            $modules = $modules->toArray();
        }
        $this->clearActiveModules();
        $this->addActiveModules($modules);
        return $this;
    }

    /**
     * Get all the active modules
     *
     * @return array
     */
    public function getActiveModules()
    {
        return $this->_activeModules;
    }

    /**
     * Add a single active module by name
     * All values are then made unqiue as there is no point to have multiple entries for the same module
     *
     * @param string $module
     * @return Glitch_Controller_Plugin_Hmvc
     */
    public function addActiveModule($module)
    {
        if (!is_scalar($module))
        {
            throw new InvalidArgumentException('Scalar value expected!');
        }
        $this->_activeModules[] = $module;
        $this->_activeModules = array_unique($this->_activeModules);
        return $this;
    }

    /**
     * Add multiple modules at once
     *
     * @param array $modules
     * @return Glitch_Controller_Plugin_Hmvc
     */
    public function addActiveModules(array $modules)
    {
        foreach ($modules as $module)
        {
            $this->addActiveModule($module);
        }
        return $this;
    }

    /**
     * Remove a registred module
     *
     * @param string $module
     * @return Glitch_Controller_Plugin_Hmvc
     */
    public function removeActiveModule($module)
    {
        if (!is_scalar($module))
        {
            throw new InvalidArgumentException('Scalar value expected!');
        }
        $key = array_search($module, $this->_activeModules, true);
        if (false !== $key)
        {
            unset($this->_activeModules[$key]);
        }
        return $this;
    }

    /**
     * Proxy method to @link removeActiveModule()
     *
     * @param string $module
     * @return Glitch_Controller_Plugin_Hmvc
     */
    public function unsetActiveModule($module)
    {
        return $this->removeActiveModule($module);
    }

    /**
     * Remove multiple modules at once
     *
     * @param array $modules
     * @return Glitch_Controller_Plugin_Hmvc
     */
    public function removeActiveModules(array $modules)
    {
        foreach ($modules as $module)
        {
            $this->removeActiveModule($module);
        }
        return $this;
    }

    /**
     * Check if a module is registered
     *
     * @param string $module
     * @return bool
     */
    public function isActiveModule($module)
    {
        if (!is_scalar($module))
        {
            throw new InvalidArgumentException('Scalar value expected!');
        }
        return in_array($module, $this->getActiveModules());
    }

    /**
     * This action will decide what controllers will be called and in which order
     *
     * Note: this can change the expected dispatch behaviour!
     *
     * @param $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        //Check if the module is registered for HMVC
        if (!$this->isActiveModule($request->getModuleName()))
        {
            return;
        }
        //For some reason, unknown to me at this time, Zend_Controller_Request has lost all relevant info.
        //Rebuild it from scratch.
        $params = explode('/', trim($request->getPathInfo(), '/'));
        if ($params[0] === $request->getModuleName())
        {
            //Unset the module if the same, not needed further
            unset($params[0]);
        }
        $action = $request->getActionName();
        $module = $request->getModuleName();

        //Clear all params as new ones are on the way
        $request->clearParams();
        //Filter strings only
        $names = array_filter($params, 'ctype_alpha');
        //Filter digits only
        $ids   = array_filter($params, 'ctype_digit');
        if (0 === count($names) && 0 === count($ids))
        {
            //We have nothing here to work with.. Assume default actions take over
            return;
        }
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        //Set some default params
        $request->setParam('collection', false)
                ->setParam('resource', true)
                ->setParam('passthrough', true);

        if (count($names) !== count($ids))
        {
            $ids = array_pad($ids, count($names), null);
            $request->setParam('collection', true)
                    ->setParam('resource', false);
            //Possible fix for crappy Zend_Rest_Route as the action is set to index
            //Example: /rest/car should point to Hmvc/Controller/Car/"METHOD" to get a collection
            if (!in_array($action, array('get', 'put', 'post', 'delete')))
            {
                $action = strtolower($request->getMethod());
            }
        }

        //Combine all data
        $params = array_combine($names, $ids);
        $params['module'] = $module;
        $params['action'] = $action;
        $params['controller'] = $dispatcher->formatModuleName(current($names));
        //Register with the request so the controllers can work with them
        $request->setModuleName($module)
                ->setActionName($action)
                ->setParams($params);

        $delimiter = $dispatcher->getPathDelimiter();
        if (!Glitch_Registry::getConfig()->resources->hmvc->redispatch)
        {
            $controller = $dispatcher->formatModuleName(implode($delimiter, $names));
            $request->setControllerName($controller)
                    ->setParam('controller', $controller)
                    ->setDispatched(false)
                    ->setParam('passthrough', false);
            return;
        }
        $nextController = '';
        foreach ($names as $controller)
        {
            //Collect the new controller string
            $nextController .= $delimiter . $controller;
            //Modify so it fits our structure and append to the controllers we want to handle
            //We use formatModuleName as we already figured out our own structure
            $this->_controllers[] = $dispatcher->formatModuleName(trim($nextController, $delimiter));
        }
    }

    /**
     * Set the passthrough variable if needed
     *
     * @param $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        //Check if the module is registered for HMVC
        if (!$this->isActiveModule($request->getModuleName()))
        {
            return;
        }
        $pos = $this->_pos + 1;
        //Check if we are still passthrough sensitive, might be usefull to know
        if (!array_key_exists($pos, $this->_controllers))
        {
            $request->setParam('passthrough', false);
        }
    }

    /**
     * Set the controllername if needed and re-dispatch
     *
     * @param $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        //Check if the module is registered for HMVC
        if (!$this->isActiveModule($request->getModuleName()))
        {
            return;
        }
        ++$this->_pos;
        $dispatched = true;
        if (array_key_exists($this->_pos, $this->_controllers))
        {
            //Whoa! New controller to serve!
            $dispatched = false;
            $request->setControllerName($this->_controllers[$this->_pos])
                    ->setParam('controller', $this->_controllers[$this->_pos]);
        }
        $request->setDispatched($dispatched);
    }
}