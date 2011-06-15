<?php

class Decisionmodule_Controller_Location
    extends Glitch_Controller_Action_Rest
{
    public function resourceDeleteAction()
    {
        $this->getRequest()->setParam('format', 'json');
        $this->getResponse()->setSubResponseRenderer('subbie');
        return null;
    }

    public function collectionDeleteAction()
    {
        $this->getResponse()->setSubResponseRenderer('subbie');
    }
}
