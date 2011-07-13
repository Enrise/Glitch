<?php
class Demo_Controller_Example
    extends Glitch_Controller_Action_Rest
{

    public function passThrough(Glitch_Controller_Request_Rest $request, $resource)
    {
        return true;
    }

    /**
     * Test it by calling /example/example
     *
     * @return array
     */
    public function collectionGetAction()
    {
        return array('data' => array('hello' => 'world'));
    }
}
