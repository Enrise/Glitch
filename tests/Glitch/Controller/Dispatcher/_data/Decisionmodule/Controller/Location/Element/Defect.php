<?php

class Decisionmodule_Controller_Location_Element_Defect
    extends Glitch_Controller_Action_Rest
{

    public function passThrough(Glitch_Controller_Request_Rest $request, $resource)
    {
        return false;
    }

    public function collectionPutAction()
    {
        return array('data' => array('unit' => 'test'));
    }

}
