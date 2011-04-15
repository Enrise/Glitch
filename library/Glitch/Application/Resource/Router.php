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
 * Resource for initializing the router
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Router extends Zend_Application_Resource_Router
{
    /**
     * Retrieves the router
     *
     * @return Zend_Controller_Router_Abstract
     */
    public function getRouter()
    {
        if (null === $this->_router)
        {
            // Don't instantiate a URL rewriter in CLI mode
            $router = (PHP_SAPI == 'cli')
                ? new Glitch_Controller_Router_Cli()
                : new Zend_Controller_Router_Rewrite();

            // Store the router in the front controller
            $this->_bootstrap->bootstrap('FrontController');
            $front = $this->_bootstrap->getResource('FrontController');
            $front->setRouter($router);

            // Setting options only works for URL rewriting
            if ($router instanceof Zend_Controller_Router_Rewrite)
            {
                // Use parent for further initialization
                $router = parent::getRouter();
            }

            // Store now as property, not earlier; otherwise parent call fails!
            $this->_router = $router;
        }
        return $this->_router;
    }
}