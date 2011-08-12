<?php

// require_once 'Zend/Image/Adapter/ImageMagick/Action/ActionAbstract.php';

class Glitch_Image_Adapter_ImageMagick_Action_Rotate
    extends Glitch_Image_Adapter_ImageMagick_Action_ActionAbstract
{

    public function perform(Glitch_Image_Adapter_ImageMagick $adapter,
        Glitch_Image_Action_Rotate $rotate)
    {
        $handle = $adapter->getHandle();

        $angle = $rotate->getAngle();
        $background = new ImagickPixel($rotate->getBackgroundColor(true));

        $handle->rotateImage($background, $angle);

        return $handle;
    }

}
