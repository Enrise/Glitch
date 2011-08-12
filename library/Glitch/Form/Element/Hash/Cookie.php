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
 * @package     Glitch
 * @author      4worx <info@4worx.com>
 * @copyright   2009, 4worx
 * @version     $Id$
 */

/**
 * Concrete class for settings form element hashes in cookies
 *
 * @category    Glitch
 * @package     Glitch_Form
 * @subpackage  Element
 */
class Glitch_Form_Element_Hash_Cookie implements Glitch_Form_Element_Hash_Interface
{

    /**
     * Storage of the current url
     * Used in cookie path
     *
     * @var string
     */
    private $_url;

    /**
     * @link Zend_Controller_Request::getCookie
     *
     * @var array
     */
    private $_cookie;

    /**
     * Instance of Glitch_Form_Element_Hash
     * @var Glitch_Form_Element_Hash
     */
    private $_parent;

    /**
     * The storage key used in the cookie
     *
     * @var string
     */
    private $_key;

    /**
     * Validator used for isValid
     *
     * @var string
     */
    private $_validator = 'Identical';

    /**
     * Constructor
     * Parameter must be an instance of Glitch_Form_Element_Hash
     *
     * @param Glitch_Form_Element_Hash $parent
     */
    public function __construct(Glitch_Form_Element_Hash $parent)
    {
        $this->_parent = $parent;
        $this->_url = Glitch_Request::getRequest()->getServer('REQUEST_URI');
        $this->_key = 'token_' . md5(__CLASS__);
    }

    /**
     * Set the validator for the element
     *
     * @return Glitch_Form_Element_Hash_Cookie
     */
    public function initCsrfValidator()
    {
        $cookie  = $this->getCookie();
        $rightHash = null;
        if (isset($cookie[$this->_key]))
        {
           $rightHash = $cookie[$this->_key];
        }
        $this->_parent->addValidator($this->_validator, true, array($rightHash));
        //$this->_parent->getValidator($this->_validator)->setMessage('');
        //$this->_parent->removeDecorator('Errors');
        return $this;
    }

    /**
     * Init the token used
     *
     * @return void
     */
    public function initCsrfToken()
    {
        if (null !== $this->getCookie($this->_key))
        {
            $timeout = Glitch_Request::getRequest()->getServer('REQUEST_TIME')+$this->_parent->getTimeout();
            setcookie($this->_key, $this->_parent->getHash(), $timeout, $this->_url);
        }
    }

    /**
     * Return cookie information
     *
     * A specific cookie can be specified or all cookies can be retrieved when not
     * setting the key parameter
     *
     * @param string $key
     * @return array
     */
    public function getCookie($key = null)
    {
        if (null === $this->_cookie)
        {
            $this->_cookie = Glitch_Request::getRequest()->getCookie($key);
        }
        return $this->_cookie;
    }

    /**
     * Clear the cookie information
     *
     * @return Glitch_Form_Element_Hash_Cookie
     */
    public function clear()
    {
        $timeout = Glitch_Request::getRequest()->getServer('REQUEST_TIME')-$this->_parent->getTimeout();
        setcookie($this->_key, '', $timeout, $this->_url);
        return $this;
        //$this->_parent->getValidator('Identical')->clearErrorMessages();
        //$this->_parent->_isError = false;

    }
}