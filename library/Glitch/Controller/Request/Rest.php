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
 * @subpackage  Request
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
 * @subpackage  Request
 */
class Glitch_Controller_Request_Rest
    extends Zend_Controller_Request_Http
{
    const RESOURCE_TYPE_COLLECTION = 'collection';
    const RESOURCE_TYPE_ELEMENT = 'element';
    
    /**
     * @var string
     */
    protected $_resourceType;
    
    /**
     * @var array
     */
    protected $_restMappings;
    
    protected $_urlElements;
    
    protected $_parentElements;
    
    protected $_parameters;
    
    public function __construct($uri = null)
    {
        $this->_restMappings = $this->_getRestMappings();
        parent::__construct($uri);
    }
    
    protected function _getRestMappings()
    {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $router = $bootstrap->getPluginResource('router');
        return $router->getRestMappings();
    }
    
    protected function _getActiveRoute()
    {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        return $bootstrap->getResource('router')->getCurrentRoute(false);
    }
    
    public function getHttpAccept()
    {
        return $this->getServer('HTTP_ACCEPT');
    }
    
    public function getQueryString()
    {
        return $this->getServer('HTTP_QUERYSTRING');
    }
    
    public function getParentElements()
    {
        if($this->_urlElements == null) {
            $this->parseUrlElements();
        }
        
        return $this->_parentElements;
    }
    
    public function parseUrlElements()
    {
        $items = $this->_urlElements = array();

        // +2 below is for prefixing and suffixing slashes
        $pathInfo = substr($this->getPathInfo(),
                          strlen(trim($this->_getActiveRoute()->getRouteUrl(),'/'))+2); 

        // collect URL info and sets the url elements.
        $items = explode('/', $pathInfo);

        // Default defaults to default..
        if (count($items) == 0) {
            $items[] = 'default';
        }
        
        // Map serialized url data into key value pairs
        $path = '';
        while (count($items))
        {
            $k = array_shift($items);
            if (strlen($k) == 0) {
                continue;
            }

            $mapping = $this->_getRestMapping($k);
            if ($mapping === false) {
                throw new Exception("Mapping not found");
            }

            if (isset($mapping['isCollection']) && $mapping['isCollection'] == true) {
                $this->_setResourceType(self::RESOURCE_TYPE_COLLECTION);
                $resource = array_shift($items);
//            } elseif (isset($mapping['isCollection'])  && $mapping['isService'] == true) {
//                $this->_setResourceType(self::RESOURCE_TYPE_SERVICE);
//                $resource = array_shift($items);
            } else {
                $this->_setResourceType(self::RESOURCE_TYPE_ELEMENT);
                $resource = null;
            }
            
            $this->_addUrlElement($mapping['name'],
                                  $resource,
                                  $path,
                                  isset($mapping['module']) ? $mapping['module'] : null 
                                );

            // Holds the "hierarchy" for finding deeper controllers
            $path .= $mapping['name']."_";
        }

        // Parse query string and set (default) values
        parse_str($this->getQueryString(), $parameters);
        $this->_setParameters($parameters);
        
        $this->_parentElements = $this->_urlElements;
        array_pop($this->_parentElements);
    }
    
    protected function _setResourceType($resourcetype)
    {
        $this->_resourceType = $resourcetype;
    }
    
    public function getResourceType() 
    {
        return $this->_resourceType;
    }
    
    /**
     * @param  $name
     * @return bool
     */
    protected function _addUrlElement($element, $resource, $path, $module = null) {
        $this->_urlElements[] = array(
            'element' => $element,
            'resource' => $resource,
            'path' => $path,
            'module' => $module
        );
    }
    
    protected function _setParameters($parameters)
    {
        $this->_parameters = $parameters;
    }
    
    protected function _getRestMapping($name) {
        return isset ($this->_restMappings[$name])
                ? $this->_restMappings[$name]
                : false;
    }
}
