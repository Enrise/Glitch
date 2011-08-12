<?php

// require_once 'Zend/Image/Adapter/Gd/Action/ActionAbstract.php';

class Glitch_Image_Adapter_Gd_Action_Rotate
    extends Glitch_Image_Adapter_ImageMagick_Action_ActionAbstract
{

    public function perform(Glitch_Image_Adapter_Gd $adapter,
        Glitch_Image_Action_Rotate $rotate)
    {
        $handle = $adapter->getHandle();

        // By default GD turns 'the wrong way
        $angle = 360 - $rotate->getAngle();

        $color = $rotate->getBackgroundColor()->getRgb();
        $colorRes = imagecolorallocate($handle,
                        $color['red'], $color['green'], $color['blue']);

        return imagerotate($handle, $angle, $colorRes);
    }

}
