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
            throw new RuntimeException(
                'Request must be of type Glitch_Controller_Request_Rest but was '
               . get_class($request)
            );
        }

        $this->_curModule = $request->getModuleName();
        $this->setResponse($response);

        $controller = $this->_getController($request);

        foreach ($request->getParentElements() as $element) {
            $className = $element['path'].$element['element'] . "Controller";
            $className = $this->formatControllerNameByParams($element['element'], $element['module']);
            if(!$className::passThrough($element['resource'])) {
                throw new Exception ("Cannot continue");
            }
        }

        $request->setDispatched(true);
        $vars = $controller->dispatch($request);
        echo $response; //headers
        $this->_renderResponse($vars, $controller, $request);

        exit;
    }

    protected function _renderResponse($vars, $controller, $request)
    {
        if(!is_array($vars)) {
            $vars = array();
        }

        $response = $this->getResponse();
        $filename = $this->_curModule . '/views/scripts/'
                  . $controller->getActionMethod($request) . '.';
        if(($subResRenderer = $response->getSubResponseRenderer()) != '') {
            $filename .= $subResRenderer . '.';
        }

        $filename .= $response->getOutputFormat().'.phtml';

        if(!file_exists($filename)) {
            if($subResRenderer != '') {
                throw new RuntimeException(
                    'A SubResponseRenderer was set but could not be located. '
                   .'Looked for "'.$filename.'" in: '.  get_include_path()
                );
            }

            $filename = 'Glitch/Controller/Response/Renderer/'
                      . ucfirst($response->getOutputFormat()) . '.php';
        }

        $this->_renderFile($filename, $vars, $this->getResponse());
    }

    protected function _renderFile($file, $vars, $response)
    {
        $func = function($_vars, $_filename, $responseObject) {
            extract($_vars);
            return include $_filename;
        };

        return $func($vars, $file, $response);
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

    public function formatControllerNameByParams($controllername, $module = null)
    {
        if($module == null) {
            $module = $this->_curModule;
        }

        return $this->formatModuleName($module) . '_Controller_' . $this->_formatName($controllername);
    }

}
