<?php

// require_once 'Zend/Image/Adapter/ImageMagick/Action/ActionAbstract.php';

class Glitch_Image_Adapter_ImageMagick_Action_Mirror
    extends Glitch_Image_Adapter_ImageMagick_Action_ActionAbstract
{

    public function perform(Glitch_Image_Adapter_ImageMagick $adapter,
        Glitch_Image_Action_Mirror $rotate)
    {
        $handle = $adapter->getHandle();
        if($rotate->flop()) {
            $handle->flopImage();
        }

        if($rotate->flip()) {
            $handle->flipImage();
        }
    }

}
