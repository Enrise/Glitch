<?php

class Glitch_Controller_Response_RestTestCase
    extends Glitch_Controller_Response_Rest
{
    public function outputBody()
    {
        $fullContent = '';
        foreach ($this->_body as $content) {
            $fullContent .= $content;
        }

        return $fullContent;
    }
}
