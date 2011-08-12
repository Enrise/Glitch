<?php

class Glitch_Console_Getopt
    extends Zend_Console_Getopt
{
    protected static $_instances = array();

    /**
     * Polymorphic Singleton ftw
     */
    public static function getInstance($name) {
        if(!isset(self::$_instances[$name])) {
            throw new Glitch_Console_Exception_RuntimeException(
                        'No instance with name '.$name.' was set'
            );
        }

        return self::$_instances[$name];
    }

    public function saveInstance($name)
    {
        self::$_instances[$name] = $this;
        return $this;
    }

    public static function reset()
    {
        if(GLITCH_APP_ENV != 'testing') {
            trigger_error(
                'Glitch_Console_Getopt::reset() should only be called in testing mode',
                E_USER_NOTICE
            );
        }

        self::$_instances = array();
    }
}
