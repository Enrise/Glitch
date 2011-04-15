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
 * @package     Glitch
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Convenience class for easy-accessing the request object
 *
 * @category    Glitch
 * @package     Glitch
 */
class Glitch_Request
{
    /**
     * Request object
     *
     * @staticvar Zend_Controller_Request_Abstract
     */
    protected static $_request = null;

    /**
     * Gets the request object
     *
     * @return Zend_Controller_Request_Abstract
     */
    public static function getRequest()
    {
        // If no request object was set, use fallback
        if (null === self::$_request)
        {
            self::setRequest(Zend_Controller_Front::getInstance()->getRequest());
        }
        return self::$_request;
    }

    /**
     * Sets the request object
     *
     * Method primarily exists for a clean interface, as the complement of getRequest().
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public static function setRequest(Zend_Controller_Request_Abstract $request)
    {
    	self::$_request = $request;
    }

    /**
     * Checks whether the current request is a POST method
     *
     * @return boolean
     */
    public static function isPost()
    {
        return self::getRequest()->isPost();
    }

    /**
     * Gets an action parameter
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getParam($key, $default = null)
    {
        return self::getRequest()->getParam($key, $default);
    }
}