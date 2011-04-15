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
 * Resource for initializing the error handler
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_ErrorHandler extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Name of the error handler class
     *
     * This name may be overridden by extending classes in order to provide
     * a custom class. Set the new value in the child init().
     *
     * @var string
     */
    protected $_className = 'Zend_Controller_Plugin_ErrorHandler';

    /**
     * Error handler
     *
     * @var Zend_Controller_Plugin_ErrorHandler
     */
    protected $_errorHandler = null;

    /**
     * Initializes this resource
     *
     * @return Zend_Controller_Plugin_ErrorHandler|null
     */
    public function init()
    {
        return $this->getErrorHandler();
    }

    /**
     * Retrieves the error handler
     *
     * @return Zend_Controller_Plugin_ErrorHandler|null
     * @throws Glitch_Application_Resource_Exception
     */
    public function getErrorHandler()
    {
        if (null === $this->_errorHandler)
        {
            // Pull in the front controller; bootstrap first if necessary
            $this->_bootstrap->bootstrap('FrontController');
            $front = $this->_bootstrap->getResource('FrontController');

            // Ignore if no error handler is to be used
            if ($front->getParam('noErrorHandler'))
            {
                return null;
            }

            // Get existing plugin, if any, or create a new one
            $this->_errorHandler = ($front->hasPlugin($this->_className))
                ? $front->getPlugin($this->_className)
                : new $this->_className();

            // Dynamic class loading; perform sanity check
            if (!$this->_errorHandler instanceof Zend_Controller_Plugin_ErrorHandler)
            {
                throw new Glitch_Application_Resource_Exception('Class is not a valid error handler instance');
            }

            // Get the default options
            $options = array(
                'module' => $this->_errorHandler->getErrorHandlerModule(),
                'controller' => $this->_errorHandler->getErrorHandlerController(),
                'action' => $this->_errorHandler->getErrorHandlerAction(),
            );

            // Merge with user-defined options from the config
            $options = array_merge($options, $this->getOptions());

            // Be aware: values must be formatted as URI values (e.g. "default", not "Default");
            $this->_errorHandler->setErrorHandlerModule($options['module']);
            $this->_errorHandler->setErrorHandlerController($options['controller']);
            $this->_errorHandler->setErrorHandlerAction($options['action']);

            if (!$front->hasPlugin($this->_className))
            {
                // Use the same stack index as in Zend_Controller_Front::dispatch()
                $front->registerPlugin($this->_errorHandler, 100);
            }
        }
        return $this->_errorHandler;
    }
}