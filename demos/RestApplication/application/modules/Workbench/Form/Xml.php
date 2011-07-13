<?php

class Workbench_Form_Xml extends Mainflow_Form
{

    public function init()
    {
        parent::init();

        $this->setLegend('Plain Xml');

        $this->setName('form_select_language');
        $this->setAttrib('id', 'form_select_language');

        $plainXml = new Zend_Form_Element_Textarea('plain_xml');
        $plainXml->setAttrib('style', 'width:410px; height: 550px;');
        $plainXml->setDecorators(array('ViewHelper'));

        $this->addElement($plainXml);
    }

}