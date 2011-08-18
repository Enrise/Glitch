<?php
interface Glitch_Image_Action_ActionInterface {

    public function __construct($options = array());

    public function addOptions($options = array());

    public function perform(Glitch_Image_Adapter_AdapterAbstract $adapter);

    public function getName();
}
