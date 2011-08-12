<?php
// require_once 'Zend/Image/Color.php';
// require_once 'Zend/Image/Adapter/ImageMagick/Action/ActionAbstract.php';

class Glitch_Image_Adapter_ImageMagick_Action_DrawPolygon
    extends Glitch_Image_Adapter_ImageMagick_Action_ActionAbstract
{

    /**
     * Draws a polygon on the handle
     *
     * @param ImageMagick-object $handle The handle on which the polygon is drawn
     * @param Glitch_Image_Action_DrawPolygon $polygon The object that with all info
     */
    public function perform($handle, Glitch_Image_Action_DrawPolygon $polygon) { // As of ZF2.0 / PHP5.3, this can be made static.

        $points = $this->_parsePoints($polygon->getPoints());

        if ($polygon->isClosed()){
            //add first point at the end to close
            $points[count($points)] = $points[0];
        }

        $draw = new ImagickDraw();

        $draw->setStrokeColor('#' . $polygon->getStrokeColor()->getHex());

        $draw->setStrokeOpacity($polygon->getStrokeAlpha()/100);
        $draw->setStrokeWidth($polygon->getStrokeWidth());

        $strokeDashArray = $polygon->getStrokeDashPattern();
        if (count($strokeDashArray) > 0){
            $draw->setStrokeDashArray($strokeDashArray);
        }
        $draw->setStrokeDashOffset($polygon->getStrokeDashOffset());

        if($polygon->isFilled()) {
            $fillColor = $polygon->getFillColor();
            $draw->setFillColor('#' . $fillColor->getHex());
            $draw->polygon($points);

        } else {
            //Use transparent fill to render unfilled
            $draw->setFillOpacity(0);
            $draw->polyline($points);
        }

        $handle->getHandle()->drawImage($draw);
    }

    protected function _parsePoints($points) {
        $out = array();
        foreach($points as $point) {

            $out[] = array(
                'x' => $point->getX(),
                'y' => $point->getY()
            );
        }

        return $out;
    }

}
