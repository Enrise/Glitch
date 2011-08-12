<?php
class Glitch_Form_Decorator_Wrapper extends Zend_Form_Decorator_HtmlTag
{
    /**
     * Default placement: surround content
     * @var string
     */
    protected $_placement = null;

    /**
     * Render
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $clear    = $this->getOption('clear');
        $clearTag = $this->getOption('clearTag');

        $this->removeOption('clear');
        $this->removeOption('clearTag');

        $element  = $this->getElement();
        $tag      = $this->_getCloseTag($this->getTag());
        if (!$element instanceof Zend_Form_Element_Hidden && isset($element->helper) && 'formHidden' !== $element->helper)
        {
            if (method_exists($element, 'hasErrors') && $element->hasErrors())
            {
                $this->setOption('class', 'errors ' . $this->getOption('class'));
            }
            else if (method_exists($element, 'isErrors') && $element->isErrors())
            {
                $this->setOption('class', 'errors ' . $this->getOption('class'));
            }
        }

        $rendered = parent::render($content);

        if (true === $clear && !empty($clearTag))
        {
            if (false !== ($pos = strripos($rendered, $tag)))
            {
                $start = substr($rendered, 0, $pos);
                $rendered = $start . $this->getOption('clearTag') . $tag;
            }
        }
        return $rendered;
    }
}