<?php

// require_once 'Zend/Image/Adapter/ImageMagick/Action/ActionAbstract.php';

class Glitch_Image_Adapter_Gd_Action_Mirror
    extends Glitch_Image_Adapter_Gd_Action_ActionAbstract
{

    public function perform(Glitch_Image_Adapter_Gd $adapter,
        Glitch_Image_Action_Mirror $mirror)
    {
        $handle = $adapter->getHandle();

        $sizeX = $adapter->getWidth();
        $sizeY = $adapter->getHeight();

        $successFlop = true;
        if($mirror->flop()) {
            $handleNew = imagecreatetruecolor($sizeX, $sizeY);
            $successFlop = imagecopyresampled(
                              $handleNew, $handle, 0, 0, ($sizeX - 1),
                              0, $sizeX, $sizeY, 0-$sizeX, $sizeY
                           );
            $handle = $handleNew;
        }

        $successFlip = true;
        if($mirror->flip()) {
            $handleNew = imagecreatetruecolor($sizeX, $sizeY);
            $successFlip = imagecopyresampled(
                              $handleNew, $handle, 0, 0, 0, ($sizeY - 1),
                              $sizeX, $sizeY, $sizeX, 0 - $sizeY
                           );
            $handle = $handleNew;
        }

        if(!$successFlop || !$successFlip) {
            // require_once 'Zend/Image/Exception';
            throw new Glitch_Image_Exception(
                        'Was not able to mirror image as specified');
        }

        return $handle;
    }

}
