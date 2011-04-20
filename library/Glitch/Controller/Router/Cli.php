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
 * @subpackage  Router
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Class for routing CLI requests
 *
 * This class does nothing: no URL routing occurs in CLI mode.
 * However, a router is still mandatory when using ZF's MVC structure, so this
 * implementation merely exists to satisfy ZF.
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Router
 */
class Glitch_Controller_Router_Cli extends Zend_Controller_Router_Abstract
{
    /**
     * Processes a request and sets its controller and action
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function route(Zend_Controller_Request_Abstract $request)
    {
    }

    /**
     * Generates a URL path that can be used in URL creation, redirection, etc
     *
     * @param  array $userParams Options passed by a user used to override parameters
     * @param  mixed $name The name of a Route to use
     * @param  bool $reset Whether to reset to the route defaults ignoring URL params
     * @param  bool $encode Tells to encode URL parts on output
     * @return void
     */
    public function assemble($userParams, $name = null, $reset = false, $encode = true)
    {
    }
}