<?php
class Workbench_View_Helper_Formdiv extends Zend_View_Helper_FormElement
{

    public function formDiv($name, $value = null, $attribs = null)
    {
        $class = '';

        if (isset($attribs['class']) && !empty($attribs['class'])) {
             $class = 'class = "'. $attribs['class'] .'"';
        }

        return "<div $class>$value</div>";
    }

}