<?php

class Decisionmodule_Controller_Location
    extends Glitch_Controller_Action_Rest
{
    public $preDispatch = false;
    public $postDispatch = false;

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
    
    public function preDispatch()
    {
        $this->preDispatch = true;
    }
    
    public function postDispatch()
    {
        $this->postDispatch = true;
    }
}