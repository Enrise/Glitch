<?php
class Glitch_Form_Element_Text_Number extends Glitch_Form_Element_Text
{
    public function init()
    {
        if ($this->isAutoloadFilters())
        {
            $this->addFilter('Digits');
        }

        if ($this->isAutoloadValidators())
        {
            $this->addValidator('Digits');
            $validatorOpts = array_filter(array(
                'min' => $this->getAttrib('min'),
                'max' => $this->getAttrib('max'),
            ));
            $validator = null;
            if (2 === count($validatorOpts))
            {
                $validator = 'Between';
            }
            else if (isset($validatorOpts['min']))
            {
                $validator = 'GreaterThan';
            }
            else if (isset($validatorOpts['max']))
            {
                $validator = 'LessThan';
            }
            var_dump($validatorOpts);
            if (null !== $validator)
            {
                $this->addValidator($validator, false, $validatorOpts);
            }
        }
    }
}