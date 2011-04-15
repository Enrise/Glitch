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
 * Resource for initializing the request
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Request extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Request object
     *
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request = null;

    /**
     * Initializes this resource
     *
     * @return Zend_Controller_Request_Abstract
     */
    public function init()
    {
        return $this->getRequest();
    }

    /**
     * Retrieves the request object
     *
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        if (null === $this->_request)
        {
            $options = $this->getOptions();

            // Don't instantiate an HTTP request in CLI mode
            if (PHP_SAPI == 'cli')
            {
                $this->_request = new Zend_Controller_Request_Simple();
            }
            else
            {
                // Load Apache-specific request object if applicable
                $this->_request = (function_exists('apache_get_version'))
                    ? new Zend_Controller_Request_Apache404()
                    : new Zend_Controller_Request_Http();
            }

            // Store the config settings, if any, in the request
            foreach ($options as $key => $value)
            {
                $method = 'set' . $key; // E.g. "setBaseUrl", "setParams"
                if (method_exists($this->_request, $method))
                {
                    $this->_request->$method($value);
                }
                else // Not a method, set as action parameter
                {
                    $this->_request->setParam($key, $value);
                }
            }

            // Store the request in the front controller
            $this->_bootstrap->bootstrap('FrontController');
            $front = $this->_bootstrap->getResource('FrontController');
            $front->setRequest($this->_request);
        }
        return $this->_request;
    }
}