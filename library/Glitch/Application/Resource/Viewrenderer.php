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
 * Resource for setting view rendering options
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_ViewRenderer extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Name of the view renderer class
     *
     * This name may be overridden by extending classes in order to provide
     * a custom class. Set the new value in the child init().
     *
     * @var string
     */
    protected $_className = 'Zend_Controller_Action_Helper_ViewRenderer';

    /**
     * View renderer
     *
     * @var Zend_Controller_Action_Helper_ViewRenderer
     */
    protected $_viewRenderer = null;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Controller_Action_Helper_ViewRenderer|null
     */
    public function init()
    {
        return $this->getViewRenderer();
    }

    /**
     * Retrieves the view renderer object
     *
     * @return Zend_Controller_Action_Helper_ViewRenderer|null
     * @throws Glitch_Application_Resource_Exception
     */
    public function getViewRenderer()
    {
        if (null === $this->_viewRenderer)
        {
            // Pull in the front controller; bootstrap first if necessary
            $this->_bootstrap->bootstrap('FrontController');
            $front = $this->_bootstrap->getResource('FrontController');

            // Ignore if no view renderer is to be used
            if ($front->getParam('noViewRenderer'))
            {
                return null;
            }

            // Get existing renderer, if any, or create a new one
            $this->_viewRenderer = (Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer'))
                ? Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')
                : new $this->_className(null, $this->getOptions());

            // Dynamic class loading; perform sanity check
            if (!$this->_viewRenderer instanceof Zend_Controller_Action_Helper_ViewRenderer)
            {
                throw new Glitch_Application_Resource_Exception('Class is not a valid view renderer instance');
            }

            // Register the view as the default view for handling view scripts
            $this->_bootstrap->bootstrap('View');
            $view = $this->_bootstrap->getResource('View');
            $this->_viewRenderer->setView($view);

            // It is paramount to set this base path spec: ZF otherwise uses its own
            // spec, causing it to create a path to a conventional ZF-style directory
            $this->_viewRenderer->setViewBasePathSpec(':module/' . Glitch_View::PATH_VIEW);

            // Set empty inflector settings: all path translations are handled by the custom dispatcher
            $inflector = new Zend_Filter_Inflector();
            $inflector->addRules(array(
                ':module' => array(),
                ':controller' => array(),
                ':action' => array(),
            ));
            $this->_viewRenderer->setInflector($inflector, true);

            if (!Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer'))
            {
                Zend_Controller_Action_HelperBroker::addHelper($this->_viewRenderer);
            }
        }
        return $this->_viewRenderer;
    }
}