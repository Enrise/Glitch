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
 * @package     Glitch_Application
 * @subpackage  Resource
 * @author      Dolf Schimmel (Freeaqingme) <dolf@enrise.com>
 * @copyright   2011, Enrise
 * @license     http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Action Controller that acts as a base class for
 * all Action Controllers implementing REST
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Router extends Zend_Application_Resource_Router
{
    /**
     * @var array
     */
    protected $_restMappings = null;

    /**
     * Retrieves the router
     *
     * @return Zend_Controller_Router_Abstract
     */
    public function getRouter()
    {
        if (null === $this->_router)
        {
            // Store the router in the front controller
            $this->_bootstrap->bootstrap('FrontController');
            $front = $this->_bootstrap->getResource('FrontController');

            // Don't instantiate a URL rewriter in CLI mode
            if ( ($router = $front->getRouter()) != null) {
                if ($this->_getPhpSapi() == 'cli' &&
                    $this->_getApplicationEnvironment() != 'testing')
                {
                    $front->setRouter(($router = new Glitch_Controller_Router_Cli()));
                } elseif ( ! ($front instanceof Glitch_Controller_Front &&
                              !$front->isRouterSet()))
                {
                    $front->setRouter(($router = new Glitch_Controller_Router_Rewrite()));
                }
            }

            // Setting options only works for URL rewriting
            if ($router instanceof Zend_Controller_Router_Rewrite)
            {
                // Use parent for further initialization
                $router = parent::getRouter();
            }

            // Store now as property, not earlier; otherwise parent call fails!
            $this->_router = $router;
            $this->_initRestMappings();
        }

        return $this->_router;
    }

    protected function _initRestMappings()
    {
        $options = $this->getOptions();
        if (isset($options['restmappings']) && $options['restmappings'] !== null)
        {
            $this->_restMappings = $options['restmappings'];
        }
    }

    /**
     *
     * @return array
     * @throws \RuntimeException if mappings have not been set
     */
    public function getRestMappings()
    {
        $mappings = $this->_getRestMappings();
        if(null === $mappings) {
            throw new \RuntimeException(
                'The rest mappings were tried to retrieve but have not been set'
            );
        }

        return $mappings;
    }

    public function hasRestMappings()
    {
        $mappings = $this->_getRestMappings();
        return ! (null == $mappings ||
                  (is_array($mappings) && 0 == count($mappings)));
    }

    protected function _getRestMappings()
    {
        if($this->_restMappings == null) {
            if (null === $this->_router) {
                $this->getRouter();
                return $this->_getRestMappings();
            }
        }

        return $this->_restMappings;
    }

    /*
     * This method was added to allow for testing
     */
    protected function _getPhpSapi()
    {
        return PHP_SAPI;
    }

    /*
     * This method was added to allow for testing
     */
    protected function _getApplicationEnvironment()
    {
        return GLITCH_APP_ENV;
    }
}
