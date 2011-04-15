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
 * Concrete class for handling form elements that are using hashes
 *
 * @category    Glitch
 * @package     Glitch_Form
 * @subpackage  Element
 */
class Glitch_Form_Element_Hash extends Zend_Form_Element_Hash implements Glitch_Form_Element_Hash_Interface
{

    /**
     * Adapter for hash elements
     *
     * @var Glitch_Form_Element_Hash_Interface
     */
    private $_adapter;

    /**
     * Constructor that checks if an adapter isset
     *
     * @param $spec
     * @param $options
     */
    public function __construct($spec, $options = null)
    {
        if (isset($options['adapter']))
        {
            $this->setAdapter($options['adapter']);
            unset($options['adapter']);//Else the option is taken into account when constructing parent, duh..
        }
        parent::__construct($spec, $options);
    }

    /**
     * Returns the currently set adapter
     *
     * @return Glitch_Form_Element_Hash_Interface
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Set an adapter for the hash element to base its storage on
     *
     * @param $adapter
     * @return Glitch_Form_Element_Hash
     */
    public function setAdapter($adapter)
    {
        if (is_string($adapter))
        {
            $adapter = new $adapter($this);
        }
        if (!$adapter instanceof Glitch_Form_Element_Hash_Interface)
        {
            throw new Glitch_Exception('Adapter needs to be an instance of Glitch_Form_Element_Hash_Interface!');
        }
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Initialize CSRF validator
     *
     * Initializes CSRF token in given adapter storage or session if no adapter is set.
     * Additionally, adds validator for validating CSRF token.
     *
     * @return Glitch_Form_Element_Hash
     */
    public function initCsrfValidator()
    {
        if (null !== $this->getAdapter())
        {
             $this->getAdapter()->initCsrfValidator();
        }
        else
        {
            parent::initCsrfValidator();
        }
        return $this;
    }

    /**
     * Initialize CSRF token in adapter or session if adapter is not set
     *
     * @return void
     */
    public function initCsrfToken()
    {
        if (null !== $this->getAdapter())
        {
            $this->_adapter->initCsrfToken();
        }
        else
        {
            parent::initCsrfToken();
        }
    }

    /**
     * Clear the hash data of the adapter
     *
     * @return Glitch_Form_Element_Hash
     */
    public function clear()
    {
        //@todo: check if no adapter isset..
        if (null !== $this->getAdapter())
        {
            $this->getAdapter()->clear();
        }
        return $this;
    }

}