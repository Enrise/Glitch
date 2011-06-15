<?php

class Glitch_Controller_FrontMock
    extends Glitch_Controller_Front
{
    public static function resetHard()
    {
        static::getInstance()->resetInstance();
        self::$_instance = null;
        Glitch_Controller_Front::getInstance();
    }
}