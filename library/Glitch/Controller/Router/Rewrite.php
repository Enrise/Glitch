<?php
/**
 * Glitch
 *
 * Copyright (c) 2011, Enrise BV (www.enrise.com).
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of 4worx nor the names of his contributors
 *     may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Dispatcher
 * @author      Dolf Schimmel (Freeaqingme) <dolf@enrise.com>
 * @copyright   2011, Enrise
 * @license     http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Action Controller that acts as a base class for
 * all Action Controllers implementing REST
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Dispatcher
 */
class Glitch_Controller_Router_Rewrite extends Zend_Controller_Router_Rewrite
{
    /**
     * Find a matching route to the current PATH_INFO and inject
     * returning values to the Request object.
     *
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Request_Abstract Request object
     */
    public function route(Zend_Controller_Request_Abstract $request)
    {
        if(($curRouteName = $this->_currentRoute) != null) {
            $route = $this->getRoute($curRouteName);
            if (!method_exists($route, 'getVersion') || $route->getVersion() == 1) {
                $match = $request->getPathInfo();
            } else {
                $match = $request;
            }
            
            if($params = $route->match($match)) {
                $this->_setRequestParams($request, $params);
                
                return $request;
            }
        }
        
        return parent::route($request);
    }
    
    /**
     * Retrieve a currently matched route
     *
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Router_Route_Interface Route object
     */
    public function getCurrentRoute($throwException = true)
    {
        if($throwException) {
            return parent::getCurrentRoute();
        }
        
        if(!isset($this->_routes[$this->_currentRoute])) {
            return;
        }
        
        return $this->getRoute($this->_currentRoute);
    }
    
}
