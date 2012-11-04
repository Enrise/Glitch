<?php

namespace Glitch\Mvc\Controller;

use Zend\Mvc\Controller\ControllerManager as ZendControllerManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Mvc\Exception;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\DispatchableInterface;

/**
 * Manager for loading controllers
 *
 * Does not define any controllers by default, but does add a validator.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 */
class ControllerManager extends ZendControllerManager
{

    public function getSubControllers($controller, $directOnly = false)
    {
        return $this->getServiceLocator()->getSubClasses($controller, $directOnly);
    }

}
