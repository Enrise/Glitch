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
 * Resource for initializing the response
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Response extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Response object
     *
     * @var Zend_Controller_response_Abstract
     */
    protected $_response = null;

    /**
     * Initializes this resource
     *
     * @return Zend_Controller_Response_Abstract
     */
    public function init()
    {
        return $this->getResponse();
    }

	/**
	 * Retrieves the response object
	 *
	 * Note that you cannot pass any options to the response object: it seems
	 * to be quite useless to do so; response parameters must be set at runtime,
	 * not as part of the configuration.
	 *
	 * @return Zend_Controller_Response_Abstract
	 */
    public function getResponse()
    {
        if (null === $this->_response)
        {
	        // Don't instantiate an HTTP request in CLI mode
	        $this->_response = (PHP_SAPI == 'cli')
	            ? new Zend_Controller_Response_Cli()
	            : new Zend_Controller_Response_Http();

            // Store the request in the front controller
            $this->_bootstrap->bootstrap('FrontController');
            $front = $this->_bootstrap->getResource('FrontController');
	        $front->setResponse($this->_response);
        }
        return $this->_response;
    }
}