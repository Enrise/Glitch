<?php
// require_once 'Zend/Image/Color.php';
// require_once 'Zend/Image/Adapter/Gd/Action/ActionAbstract.php';

class Glitch_Image_Adapter_Gd_Action_DrawEllipse
    extends Glitch_Image_Adapter_Gd_Action_ActionAbstract
{

    /**
     * Draws an ellipse on the handle
     *
     * @param GD-object $handle The handle on which the ellipse is drawn
     * @param Glitch_Image_Action_DrawEllipse $ellipseObject The object that with all info
     */
    public function perform($handle, Glitch_Image_Action_DrawEllipse $ellipseObject) { // As of ZF2.0 / PHP5.3, this can be made static.

        if($ellipseObject->filled()){
            $color = $ellipseObject->getFillColor()->getRgb();
            $alpha = $ellipseObject->getFillAlpha();
        }else{
            $color = $ellipseObject->getStrokeColor()->getRgb();
            $alpha = $ellipseObject->getStrokeAlpha();
        }

        $colorAlphaAlloc =     imagecolorallocatealpha($handle->getHandle(),
                                                        $color['red'],
                                                       $color['green'],
                                                       $color['blue'],
                                                       127 - $alpha * 1.27);

        if($ellipseObject->filled()) {
            imagefilledellipse($handle->getHandle(),
                               $ellipseObject->getLocation()->getX(),
                               $ellipseObject->getLocation()->getY(),
                               $ellipseObject->getWidth(),
                               $ellipseObject->getHeight(),
                               $colorAlphaAlloc);
        } else {
            imageellipse($handle->getHandle(),
                         $ellipseObject->getLocation()->getX(),
                         $ellipseObject->getLocation()->getY(),
                         $ellipseObject->getWidth(),
                         $ellipseObject->getHeight(),
                         $colorAlphaAlloc);
        }
    }

}
