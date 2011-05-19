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
 * @subpackage  Plugin
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
 * @subpackage  Action
 */
abstract class Glitch_Controller_Action_Rest
    extends Zend_Controller_Action
    implements Zend_Controller_Action_Interface
{

    /**
     * Array of arguments provided to the constructor, minus the
     * {@link $_request Request object}.
     * @var array
     */
    protected $_invokeArgs = array();

    /**
     * Front controller instance
     * @var Zend_Controller_Front
     */
    protected $_frontController;

    /**
     * Zend_Controller_Request_Abstract object wrapping the request environment
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request = null;

    /**
     * Zend_Controller_Response_Abstract object wrapping the response
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response = null;

    /*
     * Helper Broker to assist in routing help requests to the proper object
     *
     * @var Zend_Controller_Action_HelperBroker
     */
    protected $_helper = null;


    public function dispatch($request)
    {
        return $this->{$this->getActionMethod($request)}();
    }

    public function getActionMethod(Glitch_Controller_Request_Rest $request)
    {
//        if(!$request instanceof App_Controller_Request_Rest) {
//            throw new \RuntimeException(
//                'Supplied argument must be an instance of '
//               .' Glitch_Controller_Request_Rest, but ' . get_class($request)
//               .' was given');
//        }

        return $request->getResourceType()
              . ucfirst(strtolower($request->getMethod()))
              . 'Action';
    }

    /**
     * @param type $request
     * @return bool|stdClass
     */
    public static function passThrough($request)
    {
        return true;
    }

    public function collectionGetAction() {
        return $this->notImplementedAction();
    }

    public function resourceGetAction() {
        return $this->notImplementedAction();
    }

    public function collectionPutAction() {
        return $this->notImplementedAction();
    }

    public function resourcePutAction() {
        return $this->notImplementedAction();
    }

    public function collectionDeleteAction() {
        return $this->notImplementedAction();
    }

    public function resourceDeleteAction() {
        return $this->notImplementedAction();
    }

    public function collectionPostAction() {
        return $this->notImplementedAction();
    }

    public function resourcePostAction() {
        return $this->notImplementedAction();
    }

    public function collectionOptionsAction() {
        return $this->notImplementedAction();
    }

    public function resourceOptionsAction() {
        return $this->notImplementedAction();
    }

    public function notImplementedAction()
    {
        $this->getResponse()->setHttpResponseCode(501);
    }

    public function __call($function, $args)
    {
        return $this->notImplementedAction();
    }
}
