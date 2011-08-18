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
    const CONSOLE_GETOPT_KEY = 'Glitch_Controller_Router_Cli';

    /**
     * Processes a request and sets its controller and action
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function route(Zend_Controller_Request_Abstract $request)
    {
        $console = Glitch_Console_Getopt::getInstance(self::CONSOLE_GETOPT_KEY);

        // Make sure the request is properly formatted
        $reqString = $console->request;
        if(empty($reqString)) {
            throw new Glitch_Controller_Router_Exception_InvalidArgumentException(
                        'No Request String found in Glitch_Console_GetOpt'
            );
        }

        $parts = array_filter(explode('.', $reqString));
        if (count($parts) != 3)
        {
            throw new Glitch_Controller_Router_Exception_InvalidArgumentException(
                        'Request is not in format module.controller.action'
            );
        }

        // Check for additional parameters to the request
        $params = array();
        if (isset($console->params))
        {
            if (function_exists('mb_parse_str'))
            {
                mb_parse_str($console->params, $params);
            } else {
                parse_str($console->params, $params);
            }
        }

        $request->setModuleName($parts[0]);
        $request->setControllerName($parts[1]);
        $request->setActionName($parts[2]);
        $request->setParams($params);
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