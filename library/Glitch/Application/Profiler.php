<?php
/**
 * Application profiler
 *
 * This class provides
 * If you want to store the profile info, create the following table
 * and the database <dbname>_profiler
 * create table profiler_log_template (
 *    ip int unsigned not null,
 *    page varchar(255) not null,
 *    user_agent VARCHAR(255) not null,
 *    referer VARCHAR(255) not null,
 *    dispatch float not null,
 *    dispatch_comment text not null,
 *    model float not null,
 *    model_comment text not null,
 *    view float not null,
 *    view_comment text not null,
 *    controller float not null,
 *    controller_comment text not null,
 *    db float not null,
 *    db_comment text not null,
 *    dba float not null,
 *    dba_comment text not null,
 *    cache float not null,
 *    cache_comment text not null,
 *    service float not null,
 *    service_comment text not null,
 *    other float not null,
 *    other_comment text not null,
 *    logdate timestamp not null default current_timestamp on update CURRENT_TIMESTAMP
 * ) engine = ARCHIVE;
 *
 * @category    Glitch
 * @package        Glitch_Application
 * @author        Jeroen van Dijk <jeroen@4worx.com>
 * @version        $Id$
 */

class Glitch_Application_Profiler
{
    const DISPATCH = 1;
    const MODEL = 2;
    const VIEW = 4;
    const CONTROLLER = 8;
    const DB = 16;
    const DBA = 32;
    const CACHE = 64;
    const SERVICE = 128;
    const OTHER = 256;

    private $_tableNames = array(
        1 => "dispatch",
        2 => "model",
        4 => "view",
        8 => "controller",
        16 => "db",
        32 => "dba",
        64 => "cache",
        128 => "service",
        256 => "other"
    );

    private static $_instance;

    /**
     * Array of Glitch_Application_Profiler_Timer objects.
     *
     * @var array
     */
    protected $_timerProfiles = array();

    /**
     * Stores enabled state of the profiler.  If set to False, calls to
     * timerStart() will simply be ignored.
     *
     * @var boolean
     */
    protected $_enabled = false;

    /**
     * Stores the number of seconds to filter.  NULL if filtering by time is
     * disabled.  If an integer is stored here, profiles whose elapsed time
     * is less than this value in seconds will be unset from
     * the self::$_timerProfiles array.
     *
     * @var integer
     */
    protected $_filterElapsedSecs = null;

    /**
     * Logical OR of any of the filter constants.  NULL if filtering by timer
     * type is disable.  If an integer is stored here, it is the logical OR of
     * any of the timer type constants.  When the timer ends, if it is not
     * one of the types specified, it will be unset from the
     * self::$_timerProfiles array.
     *
     * @var integer
     */
    protected $_filterTypes = null;

    /**
     * Class constructor.  The profiler is disabled by default unless it is
     * specifically enabled by passing in $enabled here or calling setEnabled().
     *
     * @param  boolean $enabled
     * @return void
     */
    private function __construct($enabled = false)
    {
        $this->setEnabled($enabled);
    }

    /**
     * factory
     *
     * @return    instance
     */
    public static function factory($enabled = false)
    {
        if (!self::$_instance)
        {
            self::$_instance = new self($enabled);
        }
        return self::$_instance;
    }

    /**
     * getInstance
     *
     * @return    instance
     */
    public static function getInstance()
    {
        if (!self::$_instance)
        {
            self::$_instance = self::factory();
        }
        return self::$_instance;
    }

    /**
     * Enable or disable the profiler.  If $enable is false, the profiler
     * is disabled and will not log any queries sent to it.
     *
     * @param  boolean $enable
     * @return Zend_Applcation_Profiler Provides a fluent interface
     */
    public function setEnabled($enable)
    {
        $this->_enabled = (boolean) $enable;

        return $this;
    }

    /**
     * Get the current state of enable.  If True is returned,
     * the profiler is enabled.
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Sets a minimum number of seconds for saving timer profiles.  If this
     * is set, only those timers whose elapsed time is equal or greater than
     * $minimumSeconds will be saved.  To save all timers regardless of
     * elapsed time, set $minimumSeconds to null.
     *
     * @param  integer $minimumSeconds OPTIONAL
     * @return Glitch_Application_Profiler Provides a fluent interface
     */
    public function setFilterElapsedSecs($minimumSeconds = null)
    {
        if (null === $minimumSeconds)
        {
            $this->_filterElapsedSecs = null;
        }
        else
        {
            $this->_filterElapsedSecs = (integer) $minimumSeconds;
        }

        return $this;
    }

    /**
     * Returns the minimum number of seconds for saving timer profiles, or null if
     * timer profiles are saved regardless of elapsed time.
     *
     * @return integer|null
     */
    public function getFilterElapsedSecs()
    {
        return $this->_filterElapsedSecs;
    }

    /**
     * Sets the types of timer profiles to save.  Set $timerType to one of
     * the Glitch_Application_Profiler::* constants to only save profiles for that type of
     * timer.  To save more than one type, logical OR them together.  To
     * save all timers regardless of type, set $timerType to null.
     *
     * @param  integer $timerTypes OPTIONAL
     * @return Glitch_Application_Profiler Provides a fluent interface
     */
    public function setFilterTimerType($timerTypes = null)
    {
        $this->_filterTypes = $timerTypes;

        return $this;
    }

    /**
     * Returns the types of timer profiles saved, or null if timers are saved regardless
     * of their types.
     *
     * @return integer|null
     * @see    Glitch_Application_Profiler::setFilterTimerType()
     */
    public function getFilterTimerType()
    {
        return $this->_filterTypes;
    }

    /**
     * Clears the history of any past timer profiles.  This is relentless
     * and will even clear timers that were started and may not have
     * been marked as ended.
     *
     * @return Glitch_Application_Profiler Provides a fluent interface
     */
    public function clear()
    {
        $this->_timerProfiles = array();

        return $this;
    }

    /**
     * @param  integer $timerId
     * @return integer or null
     */
    public function timerClone(Glitch_Application_Profiler_Timer $timer)
    {
        $this->_timerProfiles[] = clone $timer;

        end($this->_timerProfiles);

        return key($this->_timerProfiles);
    }

    /**
     * Starts a timer.  Creates a new timer profile object (Glitch_Application_Profiler_Timer)
     * and returns the "timer profiler handle".  Run the timer, then call
     * timerEnd() and pass it this handle to make the timer as ended and
     * record the time.  If the profiler is not enabled, this takes no
     * action and immediately returns null.
     *
     * @param  integer $timerType   OPTIONAL Type of timer, one of the Glitch_Application_Profiler::* constants
     * @param  string  $timerComment    info related to the timer
     * @return integer|null
     */
    public function timerStart($timerType = null, $timerComment = "")
    {
        if (!$this->_enabled || null === $timerType)
        {
            return null;
        }

        /**
         * @see Glitch_Application_Profiler_Timer
         */
        require_once 'Glitch/Application/Profiler/Timer.php';
        $this->_timerProfiles[] = new Glitch_Application_Profiler_Timer($timerType, $timerComment);

        end($this->_timerProfiles);

        return key($this->_timerProfiles);
    }

    /**
     * Ends a timer.  Pass it the handle that was returned by timerStart().
     * This will mark the timer as ended and save the time.
     *
     * @param  integer $timerId
     * @param  string $timerComment
     * @throws Glitch_Application_Profiler_Exception
     * @return void
     */
    public function timerEnd($timerId, $timerComment = null)
    {
        // Don't do anything if the Glitch_Application_Profiler is not enabled.
        if (!$this->_enabled) {
            return;
        }

        // Check for a valid timer handle.
        if (!isset($this->_timerProfiles[$timerId])) {
            /**
             * @see Glitch_Application_Profiler_Exception
             */
            require_once 'Glitch/Application/Profiler/Exception.php';
            throw new Glitch_Application_Profiler_Exception("Profiler has no timer with handle '$timerId'.");
        }

        $tp = $this->_timerProfiles[$timerId];

        // Ensure that the timer profile has not already ended
        if ($tp->hasEnded()) {
            /**
             * @see Glitch_Application_Profiler_Exception
             */
            require_once 'Glitch/Application/Profiler/Exception.php';
            throw new Glitch_Application_Profiler_Exception("Timer with profiler handle '$timerId' has already ended.");
        }

        // End the timer profile so that the elapsed time can be calculated.
        $tp->end($timerComment);

        /**
         * If filtering by elapsed time is enabled, only keep the profile if
         * it ran for the minimum time.
         */
        if (null !== $this->_filterElapsedSecs && $tp->getElapsedSecs() < $this->_filterElapsedSecs) {
            unset($this->_timerProfiles[$timerId]);
            return;
        }

        /**
         * If filtering by timer type is enabled, only keep the timer if
         * it was one of the allowed types.
         */
        if (null !== $this->_filterTypes && !($tp->getTimerType() & $this->_filterTypes)) {
            unset($this->_timerProfiles[$timerId]);
            return;
        }
    }

    /**
     * Get a profile for a timer.  Pass it the same handle that was returned
     * by timerStart() and it will return a Glitch_Application_Profiler_Timer object.
     *
     * @param  integer $timerId
     * @throws Glitch_Application_Profiler_Exception
     * @return Glitch_Application_Profiler_Timer
     */
    public function getTimerProfile($timerId)
    {
        if (!array_key_exists($timerId, $this->_timerProfiles)) {
            /**
             * @see Glitch_Application_Profiler_Exception
             */
            require_once 'Glitch/Application/Profiler/Exception.php';
            throw new Glitch_Application_Profiler_Exception("Timer handle '$timerId' not found in profiler log.");
        }

        return $this->_timerProfiles[$timerId];
    }

    /**
     * Get an array of timer profiles (Glitch_Application_Profiler_Timer objects).  If $timerType
     * is set to one of the Glitch_Application_Profiler::* constants then only timers of that
     * type will be returned.  Normally, timers that have not yet ended will
     * not be returned unless $showUnfinished is set to True.  If no
     * queries were found, False is returned. The returned array is indexed by the timer
     * profile handles.
     *
     * @param  integer $timerType
     * @param  boolean $showUnfinished
     * @return array|false
     */
    public function getTimerProfiles($timerType = null, $showUnfinished = false)
    {
        $timerProfiles = array();
        foreach ($this->_timerProfiles as $key => $tp)
        {
            if ($timerType === null)
            {
                $condition = true;
            }
            else
            {
                $condition = ($tp->getTimerType() & $timerType);
            }

            if (($tp->hasEnded() || $showUnfinished) && $condition)
            {
                $timerProfiles[$key] = $tp;
            }
        }

        if (empty($timerProfiles)) {
            $timerProfiles = false;
        }

        return $timerProfiles;
    }

    /**
     * Get the total elapsed time (in seconds) of all of the profiled queries.
     * Only queries that have ended will be counted.  If $timerType is set to
     * one or more of the Glitch_Application_Profiler::* constants, the elapsed time will be calculated
     * only for queries of the given type(s).
     *
     * @param  integer $timerType OPTIONAL
     * @return float
     */
    public function getTotalElapsedSecs($timerType = null)
    {
        $elapsedSecs = 0;
        foreach ($this->_timerProfiles as $key => $tp)
        {
            if (null === $timerType)
            {
                $condition = true;
            }
            else
            {
                $condition = ($tp->getTimerType() & $timerType);
            }
            if (($tp->hasEnded()) && $condition)
            {
                $elapsedSecs += $tp->getElapsedSecs();
            }
        }
        return $elapsedSecs;
    }

    /**
     * Get the total number of queries that have been profiled.  Only queries that have ended will
     * be counted.  If $timerType is set to one of the Glitch_Application_Profiler::* constants, only queries of
     * that type will be counted.
     *
     * @param  integer $timerType OPTIONAL
     * @return integer
     */
    public function getTotalNumQueries($timerType = null)
    {
        if (null === $timerType)
        {
            return count($this->_timerProfiles);
        }

        $numQueries = 0;
        foreach ($this->_timerProfiles as $tp)
        {
            if ($tp->hasEnded() && ($qp->getTimerType() & $timerType))
            {
                $numQueries++;
            }
        }

        return $numQueries;
    }

    /**
     * Get the Glitch_Application_Profiler_Timer object for the last timer that was run, regardless if it has
     * ended or not.  If the timer has not ended, its end time will be null.  If no queries have
     * been profiled, false is returned.
     *
     * @return Glitch_Application_Profiler_Timer|false
     */
    public function getLastTimerProfile()
    {
        if (empty($this->_timerProfiles))
        {
            return false;
        }

        end($this->_timerProfiles);

        return current($this->_timerProfiles);
    }

    /**
     * Store the profile info
     *
     * @param    Zend_Db_Adapter_Abstract $db
     * @return    void
     */
    public function saveProfileInfo(Zend_Db_Adapter_Abstract $db = null, Zend_Controller_Request_Abstract $request)
    {
        if ((($db instanceof Zend_Db_Adapter_Pdo_Mysql) ||
            ($db instanceof Zend_Db_Adapter_Mysqli))
            && $this->_enabled
        )
        {
            $values = array();
            $values["ip"] = new Zend_Db_Expr('inet_aton("' . $request->getServer('REMOTE_ADDR', '') . '")');
            $values["page"] = $request->getServer('REQUEST_URI', '');
            $values["user_agent"] = $request->getServer('HTTP_USER_AGENT', '');
            $values["referer"] = $request->getServer('HTTP_REFERER', '');
            foreach ($this->_tableNames as $key => $value)
            {
                $values[$value] = 0;
                $values[$value."_comment"] = "";
            }

            foreach ($this->_timerProfiles as $key => $tp)
            {
                if ($tp->hasEnded())
                {
                    $values[$this->_tableNames[$tp->getTimerType()]] += $tp->getElapsedSecs();
                    $values[$this->_tableNames[$tp->getTimerType()] . "_comment"] .= $tp->getTimerComment();
                }
            }

            $dbname = "profiler";
            $config = $db->getConfig();
            if (isset($config["dbname"]))
            {
                $dbname = (strpos($config["dbname"], $dbname) !== false) ? $config["dbname"] : $config["dbname"]."_profiler";
            }

            $tableName = $dbname.".profiler_log_".date("Ymd");

            try
            {
                /**
                 * Catch table not exists error
                 * faster then checking if table exists
                 */
                $db->insert($tableName, $values);
            }
            catch (Zend_Db_Statement_Mysqli_Exception $zdsmex)
            {
                if (preg_match("/Mysqli prepare error: Table '(.*)' doesn't exist/", $zdsmex->getMessage()))
                {
                    $db->query("create table ".$tableName." like ".$dbname.".profiler_log_template");
                    $db->insert($tableName, $values);
                }
                else
                {
                    throw $zdsmex;
                }
            }
            catch (Zend_Db_Statement_Exception $zdsex)
            {
                if (preg_match("/SQLSTATE\[42S02\]: Base table or view not found: 1146 Table '(.*)' doesn't exist/", $zdsex->getMessage()))
                {
                    $db->query("create table ".$tableName." like ".$dbname.".profiler_log_template");
                    $db->insert($tableName, $values);
                }
                else
                {
                    throw $zdsex;
                }
            }
            catch (Exception $ex)
            {
                throw $ex;
            }
        }
    }
}