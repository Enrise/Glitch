<?php
// require_once 'Zend/Image/Adapter/ImageMagick/Action/ActionAbstract.php';
// require_once 'Zend/Image/Color.php';

class Glitch_Image_Adapter_ImageMagick_Action_DrawArc
    extends Glitch_Image_Adapter_ImageMagick_Action_ActionAbstract
{

    /**
     * Draws an arc on the handle
     *
     * @param Glitch_Image_Adapter_ImageMagick $handle The handle on which the ellipse is drawn
     * @param Glitch_Image_Action_DrawEllipse $ellipseObject The object that with all info
     */
    public function perform(Glitch_Image_Adapter_ImageMagick $adapter,
        Glitch_Image_Action_DrawArc $arcObject)
    {

        $draw = new ImagickDraw();

        $color = (string)$arcObject->getFillColor();
        $draw->setStrokeColor($color);

        $location = $arcObject->getLocation($adapter);

        $cx = $location->getX();
        $cy = $location->getY();
        $width = $arcObject->getWidth();
        $height = $arcObject->getHeight();

        $sx = $cx - $width / 2;
        $ex = $cx + $width / 2;

        $sy = $cy - $height / 2;
        $ey = $cy + $height / 2;

        //$draw->arc($sx, $sy, $ex, $ey, $arcObject->getCutoutStart(), $arcObject->getCutoutEnd());
//        $draw->arc($sx, $sy, $ex, $ey, 90, 315);
        $draw->arc($sx, $sy, $ex, $ey, 90, 270);

        $adapter->getHandle()->drawImage($draw);

    }
}
