<?php
class Glitch_Form_Element_Text_Email extends Glitch_Form_Element_Text
{
    public function init()
    {
        if ($this->isAutoloadValidators())
        {
            $this->addValidator('EmailAddress');
        }
    }

}