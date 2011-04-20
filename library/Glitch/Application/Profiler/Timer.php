<?php
/**
 * Application profiler timer
 *
 * This class provides 
 *
 * @category	Glitch
 * @package		Glitch_Application
 * @subpackage	Profiler
 * @author		Jeroen van Dijk <jeroen@4worx.com>
 * @version		$Id$
 */
class Glitch_Application_Profiler_Timer
{

    /**
     * User comment, set by $timerComment argument in constructor.
     *
     * @var string
     */
    protected $_comment = '';

    /**
     * One of the Glitch_Application_Profiler constants for query type, set by $timerType argument in constructor.
     *
     * @var integer
     */
    protected $_type = 0;

    /**
     * Unix timestamp with microseconds when instantiated.
     *
     * @var float
     */
    protected $_startedMicrotime = null;

    /**
     * Unix timestamp with microseconds when self::timerEnd() was called.
     *
     * @var integer
     */
    protected $_endedMicrotime = null;

    /**
     * @var array
     */
    protected $_boundParams = array();

    /**
     * @var array
     */

    /**
     * Class constructor.  A timer is about to be started, save the timer comment ($timerComment) and its
     * type (one of the Glitch_Application_Profiler::* constants).
     *
     * @param  string  $query
     * @param  integer $queryType
     * @return void
     */
    public function __construct($timerType, $timerComment)
    {
        $this->_type = $timerType;
        $this->_comment = $timerComment;
        
        // by default, and for backward-compatibility, start the click ticking
        $this->start();
    }

    /**
     * Clone handler for the timer object.
     * @return void
     */
    public function __clone()
    {
        $this->_boundParams = array();
        $this->_endedMicrotime = null;
        $this->start();
    }

    /**
     * Starts the elapsed time click ticking.
     * This can be called subsequent to object creation,
     * to restart the clock.
     *
     * @return void
     */
    public function start()
    {
        $this->_startedMicrotime = microtime(true);
    }

    /**
     * Ends the timer and records the time so that the elapsed time can be determined later.
     *
     * @return void
     */
    public function end($timerComment = null)
    {
    	if (null !== $timerComment)
    	{
    		$this->_comment .= " ".$timerComment;
    	}
        $this->_endedMicrotime = microtime(true);
    }

    /**
     * Returns true if and only if the timer has ended.
     *
     * @return boolean
     */
    public function hasEnded()
    {
        return $this->_endedMicrotime !== null;
    }

    /**
     * Get the original user comment
     *
     * @return string
     */
    public function getTimerComment()
    {
        return $this->_comment . ((strlen($this->_comment) > 0) ? ";" : "");
    }

    /**
     * Get the type of this timer (one of the Glitch_Application_Profiler::* constants)
     *
     * @return integer
     */
    public function getTimerType()
    {
        return $this->_type;
    }

    /**
     * @param string $param
     * @param mixed $variable
     * @return void
     */
    public function bindParam($param, $variable)
    {
        $this->_boundParams[$param] = $variable;
    }

    /**
     * @param array $param
     * @return void
     */
    public function bindParams(array $params)
    {
        if (array_key_exists(0, $params)) {
            array_unshift($params, null);
            unset($params[0]);
        }
        foreach ($params as $param => $value) {
            $this->bindParam($param, $value);
        }
    }

    /**
     * @return array
     */
    public function getTimerParams()
    {
        return $this->_boundParams;
    }

    /**
     * Get the elapsed time (in seconds) that the timer ran.
     * If the timer has not yet ended, false is returned.
     *
     * @return float|false
     */
    public function getElapsedSecs()
    {
        if (null === $this->_endedMicrotime) {
            return false;
        }

        return $this->_endedMicrotime - $this->_startedMicrotime;
    }
}