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
 * @subpackage  Dispatcher
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Custom dispatcher for speeding-up controller loading
 *
 * Unlike native ZF, this dispatcher assumes a one-to-one mapping between a class name
 * and its location on disk. For instance: "Default_Controller_News" ought to be located
 * in directory "Default/Controller/News.php".
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Dispatcher
 */
class Glitch_Controller_Dispatcher_Standard extends Zend_Controller_Dispatcher_Standard
{
    /**
     * Formats a controller name
     *
     * Examples:
     * - /test/what-is-where   --> Test_Controller_WhatIsWhere
     * - /test/what_is_where   --> Test_Controller_What_Is_Where
     * - /test/whatIsWhere     --> Test_Controller_Whatiswhere
     *
     * @param string $unformatted
     * @return string
     */
    public function formatControllerName($unformatted)
    {
        return $this->formatModuleName($this->_curModule) . '_Controller_' . $this->_formatName($unformatted);
    }

    /**
     * Formats a module name
     *
     * @param string $unformatted
     * @return string
     */
    public function formatModuleName($unformatted)
    {
        return $this->_formatName($unformatted);
    }

    /**
     * Loads a controller class
     *
     * Performance: prevents invocation of the expensive parent method
     *
     * @param string $className
     * @return string
     * @throws Zend_Controller_Dispatcher_Exception
     */
    public function loadClass($className)
    {
        // Autoload class if it doesn't exist
        if (false === @class_exists($className))
        {
            // Throw this specific exception to go into the error handler
            // Cannot be overriden by Glitch because ZF uses get_class() in
            // Zend_Controller_Plugin_ErrorHandler
            throw new Zend_Controller_Dispatcher_Exception(
                'Cannot load controller class "' . $className . '"', 404
            );
        }

        return $className;
    }

    /**
     * Returns true if the Zend_Controller_Request_Abstract object can be
     * dispatched to a controller
     *
     * Performance: prevents invocation of the expensive parent method
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return boolean
     */
    public function isDispatchable(Zend_Controller_Request_Abstract $request)
    {
        return (false !== $this->getControllerClass($request));
    }
}
