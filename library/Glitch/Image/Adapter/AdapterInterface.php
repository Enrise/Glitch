<?php
interface Glitch_Image_Adapter_AdapterInterface {
    public static function isAvailable();

    public function getHandle();

    public function getName();
}
