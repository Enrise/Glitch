<?php
/**
 * Glitch
 *
 * Copyright (c) 2010, Enrise BV (www.enrise.com).
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
 *   * Neither the name of Enrise nor the names of his contributors
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
 * @package     Glitch_Form
 * @author      Enrise <info@enrise.com>
 * @copyright   2010, Enrise
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @version     $Id: $
 */

/**
 * Base class for HTML5 email element
 *
 * @category    Glitch
 * @package     Glitch_Form
 */
class Glitch_Form extends Zend_Form
{
    protected $_elementDecorators = array(
        'Label',
        'ViewHelper',
        'Description',
        'Errors',
        array('Wrapper', array('class' => 'wrapper'))
    );

    public function __construct($options = null)
    {
        //Load decorators, validators for Glitch lib
        $this->addPrefixPath('Glitch_Form_', 'Glitch/Form/');
        parent::__construct($options);
    }

    /**
     * Load the default decorators
     *
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('ViewHelper', array('helper' => 'Placeholder', 'placement' => Zend_Form_Decorator_Abstract::PREPEND))
                 ->addDecorator('Fieldset', array('id' => '', 'class' => 'zend_form'))
                 ->addDecorator('Form');
        }
        return $this;
    }

    /**
     * Static function that adds all the elements in 1 wrapping element instead of dt, dd structure
     *
     * @param $elm
     * @return void
     */
    public static function setElementDecorator(Zend_Form_Element $elm)
    {
        $elm->addPrefixPath('Glitch_Form_Decorator', 'Glitch/Form/Decorator/', 'decorator');

        $labelSettings = array();
        if ($elm->getDecorator('Label'))
        {
            $labelSettings = $elm->getDecorator('Label')->getOptions();
            unset($labelSettings['tag']); //Tag should never be needed when you call this method
        }
        $class = 'wrapper';
        if ($elm->getAttrib('wrapperClass')) {
            $class .= ' ' . $elm->getAttrib('wrapperClass');
            $elm->setAttrib('wrapperClass', null);
        }
        if ($elm instanceof Zend_Form_Element_Hidden)
        {
            $class .= ' hidden';
        }
        $elm->clearDecorators()
            ->addDecorator('Label', $labelSettings)
            ->addDecorator(($elm instanceof Zend_Form_Element_File) ? "File" : "ViewHelper")
            ->addDecorator('Description', array('escape' => false))
            ->addDecorator('Errors')
            ->addDecorator('Wrapper', array(
                'tag' => 'div',
//                'id' => $elm->getName() . '-wrapper',
                'class' => $class,
        ));
        if ($elm instanceof Zend_Form_Element_Submit)
        {
            $elm->removeDecorator('Label');
        }
    }

    public function groupElements(array $elements, $name) {
        foreach ($elements as &$elm) {
            if ($elm instanceof Zend_Form_Element) {
                $elm = $elm->getName();
            }
        }
        $decorators = array(
            'decorators' => array(
                'FormElements', array('Wrapper', array(
                    'tag' => 'div',
                    'class' => 'group',
                    'id' => $name,
        ))));
        $this->addDisplayGroup($elements, $name, $decorators);
        return $this;
    }
}