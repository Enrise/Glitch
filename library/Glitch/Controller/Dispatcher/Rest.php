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
class Glitch_Controller_Dispatcher_Rest
    extends Glitch_Controller_Dispatcher_Standard
    implements Zend_Controller_Dispatcher_Interface
{
    protected $_lastController;

    protected $_lastActionMethod;

    protected $_responseRenderer;

    public function getLastController()
    {
        return $this->_lastController;
    }

    public function getLastActionMethod()
    {
        return $this->_lastActionMethod;
    }

    public function getResponseRenderer()
    {
        if (null == $this->_responseRenderer) {
            $this->_responseRenderer = new Glitch_Controller_Response_Renderer();
        }

        return $this->_responseRenderer;
    }

    public function setResponseRenderer($renderer)
    {
        $this->_responseRenderer = $renderer;
        return $this;
    }


    /**
     * Dispatches a request object to a controller/action.  If the action
     * requests a forward to another action, a new request will be returned.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @param  Zend_Controller_Response_Abstract $response
     * @return void
     */
    public function dispatch(Zend_Controller_Request_Abstract $request,
                             Zend_Controller_Response_Abstract $response)
    {
        if(!$request instanceof Glitch_Controller_Request_Rest) {
            throw new Glitch_Controller_Exception(
                'Request must be of type Glitch_Controller_Request_Rest but was '
               . get_class($request)
            );
        }

        $this->_curModule = $request->getModuleName();
        $this->setResponse($response);

        $controller = $this->_lastController = $this->_getController($request);

        foreach ($request->getParentElements() as $element) {
            $className = $this->formatControllerNameByParams($element['path'].$element['element'], $element['module']);
            if (!class_exists($className)) {
                throw new RuntimeException('Passthrough class '.$className.' could not be found');
            }

            $ptController = new $className($request, $response, $this->getParams());

            if (false === $ptController->passThrough($request, $element['resource'], $element['isCollection'])) {
                throw new Glitch_Controller_Exception('Passthrough method returned false');
            }

            unset($ptController); // Be careful with our RAM
        }

        $request->setDispatched(true);

        if (($this->_lastActionMethod = $request->getActionName()) === null) {
            $this->_lastActionMethod = $controller->dispatch($request);
            $request->setActionName($this->_lastActionMethod);

        } elseif (!method_exists($controller, $this->_lastActionMethod)) {
            $this->_lastActionMethod = $this->_lastActionMethod . 'Action';
            $request->setActionName($this->_lastActionMethod);
        }

        $vars = $controller->{$this->_lastActionMethod}();

        if($response->renderBody()) {
            $response->setBody(
                $this->getResponseRenderer()
                            ->renderResponse($response, $vars, $controller, $request)
            );
        }
    }


    public static function cloneFromDispatcher(
            Zend_Controller_Dispatcher_Interface $dispatcher)
    {
        $new = new self($dispatcher->getParams());
        $new->setControllerDirectory($dispatcher->getControllerDirectory());

        $new->setDefaultModule($dispatcher->getDefaultModule());
        $new->setDefaultControllerName($dispatcher->getDefaultControllerName());
        $new->setDefaultAction($dispatcher->getDefaultAction());

        $new->setPathDelimiter($dispatcher->getPathDelimiter());

        return $new;
    }

    /**
     * Instantiate controller with request, response, and invocation
     * arguments; throw exception if it's not a rest action controller
     */
    protected function _getController(Glitch_Controller_Request_Rest $request)
    {
        $className = $this->getControllerClass($request);

        $this->loadClass($className); // Throws exception if unloadable
        $controller = new $className($request, $this->getResponse(), $this->getParams());

        if(!$controller instanceof Glitch_Controller_Action_Rest) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception(
                'Controller "' . $className . '" is not an instance of '
              . 'Glitch_Controller_Action_Rest'
            );
        }

        return $controller;
    }

    public function getControllerClass(Zend_Controller_Request_Abstract $request)
    {
        return static::getStaticControllerClass($request);
    }

    public static function getStaticControllerClass(
                                    Zend_Controller_Request_Abstract $request)
    {
        $elements = static::getClassElements($request);

        if (0 == count($elements)) {
            $suffix = $request->getParam('controller');
        } else {
            $suffix = implode('_', $elements);
        }

        return ucfirst($request->getModuleName()) . '_Controller'
             . '_' . $suffix;
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return array
     */
    public static function getClassElements(Zend_Controller_Request_Abstract $request)
    {
        $parentElements = $request->getUrlElements();
        $out = array();

        foreach($parentElements as $parentElement) {
            $out[] = ucfirst($parentElement['element']);
        }

        return $out;
    }

    public function formatControllerNameByParams($controllername, $module = null)
    {
        if($module == null) {
            $module = $this->_curModule;
        }

        return $this->formatModuleName($module) . '_Controller_' . $this->_formatName($controllername);
    }

}
