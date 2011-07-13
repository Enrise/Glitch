<?php

require_once 'Glitch/Application/Bootstrap/Bootstrap.php';

class Bootstrap extends Glitch_Application_Bootstrap_Bootstrap
{
    /**
     * Initializes the dispatcher
     *
     * Do NOT put this code inside a dedicated resource class: the dispatcher
     * must be set as soon as possible, to prevent ZF from booting its own default
     * dispatcher. This method guarantees early invocation.
     *
     * @return Glitch_Controller_Dispatcher_Standard
     */
    protected function _initDispatcher()
    {
        $front = Zend_Controller_Front::getInstance();
        $dispatcher = $front->getDispatcher();

        if (!$dispatcher instanceof Glitch_Controller_Dispatcher_Standard) {
            $dispatcher = new Glitch_Controller_Dispatcher_Standard();
            $front->setDispatcher($dispatcher);
        }

        return $dispatcher;
    }

}
