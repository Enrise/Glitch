<?php

class Glitch_Image_Adapter_ImageMagick_Action_Resize {

    public function perform(Glitch_Image_Adapter_ImageMagick $adapter,
        Glitch_Image_Action_Resize $resize) {

        $handle = $adapter->getHandle();

        $newY = $resize->getYAmountCalculated();
        $newX = $resize->getXAmountCalculated();

        //if a dimension is 0, resize is proportionate to other dimension
        //if proportions are constrained and both dimensions are specified,
        //ImageMagick uses the smallest to constrain
        //
        //Note the fit parameter only works if the image is being made SMALLER
        //@TODO: implement constraints manually for images being made larger
        $fit = false;
        if ($resize->hasConstrainedProportions() && $newX > 0 && $newY > 0){
            $fit = true;
        }

        $handle->resizeImage($newX, $newY, $resize->getFilter(), 1, $fit);
    }

}
