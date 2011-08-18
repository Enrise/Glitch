<?php
// require_once 'Zend/Image/Color.php';
// require_once 'Zend/Image/Adapter/Gd/Action/ActionAbstract.php';
// require_once 'Zend/Image/Adapter/Gd/Action/DrawLine.php';
// require_once 'Zend/Image/Action/DrawLine.php';

class Glitch_Image_Adapter_Gd_Action_DrawPolygon
    extends Glitch_Image_Adapter_Gd_Action_ActionAbstract
{

    /**
     * Draws a polygon on the handle
     *
     * @param GD-object $handle The handle on which the polygon is drawn
     * @param Glitch_Image_Action_DrawPolygon $polygonObject The object that with all info
     */
    public function perform(Glitch_Image_Adapter_Gd $adapter, Glitch_Image_Action_DrawPolygon $polygonObject) { // As of ZF2.0 / PHP5.3, this can be made static.
        $handle = $adapter->getHandle();

        $points = $this->_parsePoints($polygonObject->getPoints());

        if(($pattern = $polygonObject->getStrokeDashPattern())!== null) {
            $color = imagecolorallocate($handle, 0, 255, 0);
            $array = array();
            foreach($pattern as $amountOfPixels) {
                $array = array_merge($array, array_fill(0, $amountOfPixels, $color));
                $array = array_merge($array, array_fill(0, $polygonObject->getStrokeDashOffset(), IMG_COLOR_TRANSPARENT));
            }

            if(count($array) > 0) {
                imagesetstyle($handle, $array);
            }
        }



        if($polygonObject->isFilled()) {
            //@TODO: extract this to Glitch_Image_Adapter_Gd_Action_ActionAbstract ?
            $color = $polygonObject->getFillColor()->getRgb();
            $colorRes = imagecolorallocatealpha($handle,
                                               $color['red'],
                                               $color['green'],
                                               $color['blue'],
                                               $polygonObject->getFillAlpha());

            imagefilledpolygon($handle, $points, count($points)/2, $colorRes);
        }

        $points = $polygonObject->getPoints();
        $start = current($points);;
        $line = new Glitch_Image_Action_DrawLine();
        while(current($points)!==false) {
            $from = current($points);
            $to = next($points);
            if($to===false) {
                if(!$polygonObject->isClosed()) {
                    break;
                } else {
                    $to = $start;
                }
            }

            $line->from($from);
            $line->to($to);
            $line->perform($adapter);
        }
    }

    /**
     * Parse the points to something the GD library understands
     *
     * @param array $points An array filled with instances of Glitch_Image_Point
     * @return An array with coordinates
     */
    protected function _parsePoints($points) {
        $out = array();
        foreach($points as $point) {
           $out[] = $point->getX();
           $out[] = $point->getY();
        }
        return $out;
    }
}
