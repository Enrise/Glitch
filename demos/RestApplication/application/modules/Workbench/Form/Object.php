<?php

class Workbench_Form_Object extends Mainflow_Form
{
    
    public function init()
    {
        parent::init();

        $this->setLegend('Address');

        $this->setName('form_select_object');
        $this->setAttrib('id', 'form_select_object');

        $postalcode = new Zend_Form_Element_Text('postalcode');
        $postalcode->setRequired(true)
                    ->setLabel('Postcode')
                    ->addFilter('StripTags')
                    ->setAttrib('class', 'small');

        $housenr = new Zend_Form_Element_Text('housenr');
        $housenr->setRequired(true)
                    ->setLabel('Huisnummer')
                    ->addFilter('StripTags')
                    ->setAttrib('class', 'tiny');

        $suffix = new Zend_Form_Element_Text('suffix');
        $suffix->setRequired(true)
                    ->setLabel('Toevoeging')
                    ->addFilter('StripTags')
                    ->setAttrib('class', 'tiny');


        $submitBack = new Zend_Form_Element_Button('submit_back');
        $submitBack->setLabel('Terug')
                    ->setDecorators(array('ViewHelper'))
                    ->setAttrib('id', 'previous')
                    ->setAttrib('type', 'submit');

        $submitNext = new Zend_Form_Element_Button('submit_next');
        $submitNext->setLabel('Volgende')
                    ->setDecorators(array('ViewHelper'))
                    ->setAttrib('id', 'next')
                    ->setAttrib('type', 'submit');

        $elements = array($postalcode, $housenr, $suffix, $submitBack, $submitNext);
        $this->addElements($elements);

        // Set these elements to elements
        $postalcode->setAttrib('wrapperClass', 'inline-field');
        $housenr->setAttrib('wrapperClass', 'inline-field');
        $suffix->setAttrib('wrapperClass', 'inline-field');

        // Group these elements together into a single <div>
        $this->groupElements(array($submitBack, $submitNext), 'navigation_wizard');

        // Must be done last unless you know what you are doing!
        array_walk($elements, array("Mainflow_Form", "setElementDecorator"));

        // Remove wrapper <div> from navigation stuff, since we already grouped them. Must be done AFTER
        // the array_walk().
        $submitBack->removeDecorator('Wrapper');
        $submitNext->removeDecorator('Wrapper');
    }
    
}