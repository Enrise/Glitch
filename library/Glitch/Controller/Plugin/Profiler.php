<?php
/**
 * Profiler controller plugin
 *
 * This plugin can be hooked into the dispatch process to allow
 * profiling on the application as a whole.
 * Specific profiling commands can be added by getting the profiler
 * instance via Glitch_Application_Profiler::getInstance and starting
 * timers via <$var>->timerStart(<Glitch_Application_Profiler::*, <comment>)
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Plugin
 * @author      Jeroen van Dijk <jeroen@4worx.com>
 * @version     $Id$
 */
class Glitch_Controller_Plugin_Profiler extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var integer
     */
    private $_timerID;

    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_timerID = Zend_Registry::get('profiler')->timerStart(Glitch_Application_Profiler::DISPATCH);
    }

    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
    	$profiler = Zend_Registry::get('profiler');

        $profiler->timerEnd($this->_timerID);

        if ($profiler->getEnabled())
        {
        	$str = '';
            foreach ($profiler->getTimerProfiles() as $key => $tp)
            {
                if ($tp->hasEnded())
                {
                    $str .= ' ' . substr($tp->getElapsedSecs(), 0, 6);
                }
            }

            $body = str_replace('</title>', $str.'</title>', $this->getResponse()->getBody());
            $this->getResponse()->setBody($body);

            /*if (Zend_Registry::isRegistered($this->_registryKey))
            {
                $db = Zend_Registry::get($this->_registryKey);

                if ($db instanceof Zend_Db_Adapter_Abstract)
                {
                    $profiler->saveProfileInfo($db, $this->getRequest());
                }
            }*/
        }
    }
}