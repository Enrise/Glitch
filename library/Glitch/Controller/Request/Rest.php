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
    const RESOURCE_TYPE_RESOURCE = 'resource';

    protected $_bootstrap;

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
        $out = $this->_restMappings;
        if($out == null) {
            $out = $this->_restMappings = $this->_getRouterAppResourcePlugin()
                                                    ->getRestMappings();
        }

        return $out;
    }

    protected function _getActiveRoute()
    {
        return $this->_getRouter()->getCurrentRoute(false);
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
            $this->_parseUrlElements();
        }

        return $this->_parentElements;
    }

    protected function _parseUrlElements()
    {
        $items = $this->_urlElements = array();

        // +2 below is for prefixing and suffixing slashes
        $pathInfo = substr($this->getPathInfo(),
                          strlen(trim($this->_getActiveRoute()->getRouteUrl(),'/'))+2);

        // collect URL info and sets the url elements.
        $items = explode('/', $pathInfo);

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
                throw new Glitch_Controller_Request_ExceptionMessage(
                    'No configuration could be found for the requested REST-mapping', 404
                );
            }

            if (isset($mapping['isCollection']) && $mapping['isCollection'] == true) {
                $this->_setResourceType(self::RESOURCE_TYPE_COLLECTION);
                $resource = null;
                $isCollection = true;
            } else {
                $this->_setResourceType(self::RESOURCE_TYPE_RESOURCE);
                $resource = array_shift($items);
                $isCollection = false;
            }

            $this->_addUrlElement($mapping['name'],
                                  $resource,
                                  $path,
                                  isset($mapping['module']) ? $mapping['module'] : null,
                                  $isCollection
                                );

            // Holds the "hierarchy" for finding deeper controllers
            $path .= $mapping['name'] . '_';
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
        if($this->_resourceType == null) {
            $this->_parseUrlElements();
        }

        return $this->_resourceType;
    }

    /**
     * @param  $name
     * @return bool
     */
    protected function _addUrlElement($element, $resource, $path, $module = null, $isCollection = false)
    {
        $this->_urlElements[] = array(
            'element' => $element,
            'resource' => urldecode($resource),
            'path' => $path,
            'module' => $module,
            'isCollection' => $isCollection
        );
    }

    protected function _setParameters($parameters)
    {
        $this->_parameters = $parameters;
    }

    public function getUrlElements()
    {
        if($this->_urlElements == null) {
            $this->_parseUrlElements();
       }

       return $this->_urlElements;
    }

     /**
     * Returns main element or rest end-point if you will.
     *
     * Returns comment/5 url element pairs from the url:
     *   /event/5/talk/3/comment/5
     *
     * @return array
     */
    public function getMainElement()
    {
        $urlElements = $this->getUrlElements();
        return $urlElements[count($urlElements)-1];
    }

    public function getResource()
    {
        $element = $this->getMainElement();
        return $element['resource'];
    }

    protected function _getRestMapping($name)
    {
        return isset ($this->_restMappings[$name])
                ? $this->_restMappings[$name]
                : false;
    }

    /**
     * Set the controller name to use
     *
     * @param string $value
     * @return Zend_Controller_Request_Abstract
     */
    public function setControllerName($value, $keepUrlElements = false)
    {
        $this->_controller = $value;
        if (! $keepUrlElements) {
            $this->_urlElements = array(0 => array('element' => $value));
            $this->_parentElements = array();
        }

        return $this;
    }

    /**
     * @throws \RuntimeException If no bootstrap is registered
     * @return Glitch_Application_Bootstrap_Bootstrap
     */
    protected function _getBootstrap()
    {
        if($this->_bootstrap == null) {
            $this->_bootstrap = Zend_Controller_Front::getInstance()
                                                    ->getParam('bootstrap');

            if($this->_bootstrap == null) {
                throw new \RuntimeException(
                    'No bootstrap was found'
                );
            }
        }

        return $this->_bootstrap;
    }

    protected function _getRouter()
    {
        return $this->_getRouterAppResourcePlugin()->getRouter();
    }

    protected function _getRouterAppResourcePlugin()
    {
        $router = $this->_getBootstrap()->getPluginResource('router');
        if($router === null) {
            throw new Glitch_Controller_Exception(
                'The router application resource plugin was not loaded'
            );
        }

        return $router;
    }

}
