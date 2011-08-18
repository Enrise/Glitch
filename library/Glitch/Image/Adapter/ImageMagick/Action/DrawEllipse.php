<?php
// require_once 'Zend/Image/Color.php';
// require_once 'Zend/Image/Adapter/ImageMagick/Action/ActionAbstract.php';

class Glitch_Image_Adapter_ImageMagick_Action_DrawEllipse
    extends Glitch_Image_Adapter_ImageMagick_Action_ActionAbstract
{

    /**
     * Draws an ellipse on the handle
     *
     * @param ImageMagick-object $handle The handle on which the ellipse is drawn
     * @param Glitch_Image_Action_DrawEllipse $ellipseObject The object that with all info
     */
    public function perform(Glitch_Image_Adapter_ImageMagick $adapter,
        Glitch_Image_Action_DrawEllipse $ellipseObject)
    {

        $draw = new ImagickDraw();

        $strokeColor = (string)$ellipseObject->getStrokeColor();
        $strokeAlpha = $ellipseObject->getStrokeAlpha() * 0.01;
        $draw->setStrokeColor($strokeColor);
        $draw->setStrokeOpacity($strokeAlpha);

        $draw->setStrokeWidth($ellipseObject->getStrokeWidth());

        $strokeDashArray = $ellipseObject->getStrokeDashPattern();
        if (count($strokeDashArray) > 0){
            $draw->setStrokeDashArray($strokeDashArray);
        }
        $draw->setStrokeDashOffset($ellipseObject->getStrokeDashOffset());

        if($ellipseObject->filled()) {
            $fillColor = (string)$ellipseObject->getFillColor();
            $draw->setFillColor($fillColor);
            $draw->setFillOpacity($ellipseObject->getFillAlpha() * 0.01);
        }else{
            $draw->setFillOpacity(0);
        }

        $width = $ellipseObject->getWidth();
        $height = $ellipseObject->getHeight();
        $x = $ellipseObject->getLocation()->getX();
        $y = $ellipseObject->getLocation()->getY();

        $draw->ellipse($x, $y, $width/2 , $height/2, 0, 360);

        $adapter->getHandle()->drawImage($draw);

    }

}
