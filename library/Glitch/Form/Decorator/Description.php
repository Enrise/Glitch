<?php

class Glitch_Form_Decorator_Description extends Zend_Form_Decorator_Description
{
    protected $_useTags = true;

    public function enableTags($useTags = true) {
        $this->_useTags = $useTags;
    }

    /**
     *  getTag override to make Zend can use empty tags
     */
    public function getTag()
    {
        // Return empty tag when we do not need tags
        if ($this->_useTags == false) {
            return '';
        }
        return parent::getTag();
    }

    /**
     * getClass override to make sure Zend doesn't output an empty class.
     *
     * @return mixed|string
     */
    public function getClass()
    {
        // When we have a non-empty class, threat is as normal
        $class = $this->getOption('class');
        if (! empty ($class)) return parent::getClass();

        /* So much fun. Apparently, we need to SET it to a non-null value before
         * removeOption will actually remove the setting. It's a dirty hack..
         */
        $this->setOption('class', 'deletebetter');
        $this->removeOption('class');
        return $class;
    }
}