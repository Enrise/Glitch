<?php

class Workbench_Form_Language extends Mainflow_Form
{

    public function init()
    {
        parent::init();

        $this->setLegend('Taal');

        $this->setName('form_select_language');
        $this->setAttrib('id', 'form_select_language');

        $langs = array(
            'nl' => 'Nederlands',
            'de' => 'Duits',
            'en' => 'Engels',
            'tr' => 'Turks',
            'fr' => 'Frans'
        );

        $elements = array();
        foreach ($langs as $iso => $lang) {

            $element = new Zend_Form_Element_Button($iso);
            $element->setLabel($lang);

            $element->setDecorators(array('ViewHelper'));
            $elements[] = $element;
        }

        $this->addElements($elements);
        $this->groupElements($elements, 'languages');
    }

}