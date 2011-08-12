<?php
/**
 * Custom Zend framework page caching functionality implementation 
 *
 * This source file is proprietary and is protected international by
 * copyright laws and trade secret laws.
 * No part of this source file may be reproduced, copied, adapted, modified,
 * distributed, transferred, translated, disclosed, displayed or otherwise used
 * by anyone in any form or by any means without the express written
 * authorization of 4worx software innovators BV (www.4worx.com) 
 *
 * @category    Glitch
 * @package     Glitch_Cache
 * @subpackage  Glitch_Cache_Frontend
 * @author      4worx <info@4worx.com>
 * @copyright   2009, 4worx
 * @version     $Id$
 */

/**
 * @category    Glitch
 * @package     Glitch_Cache
 * @subpackage  Glitch_Cache_Frontend
 * @see         Zend_Cache_Frontend_Page
 */
class Glitch_Cache_Frontend_StaticPage extends Zend_Cache_Frontend_Page
{
    /**
     * Start the cache
     *
     * @param  string  $id       (optional) A cache id (if you set a value here, maybe you have to use Output frontend instead)
     * @param  boolean $doNotDie For unit testing only !
     * @return boolean True if the cache is hit (false else)
     */
    public function start($id = false, $doNotDie = false)
    {
        $this->_cancel = false;
        $lastMatchingRegexp = null;
        foreach ($this->_specificOptions['regexps'] as $regexp => $conf) {
            if (preg_match("`$regexp`", $_SERVER['REQUEST_URI'])) {
                $lastMatchingRegexp = $regexp;
            }
        }
        $this->_activeOptions = $this->_specificOptions['default_options'];
        if ($lastMatchingRegexp !== null) {
            $conf = $this->_specificOptions['regexps'][$lastMatchingRegexp];
            foreach ($conf as $key=>$value) {
                $this->_activeOptions[$key] = $value;
            }
        }
        if (!($this->_activeOptions['cache'])) {
            return false;
        }
        if (!$id) {
            $id = $this->_makeId();
            if (!$id) {
                return false;
            }
        }
        $data = $this->load($id);
        if ($data !== false)
        {
            echo $data;
            die();
        }
        
        ob_start(array($this, '_flush'));
        ob_implicit_flush(false);
        return false;
    }
    
    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @param  boolean $doNotUnserialize       Do not serialize (even if automatic_serialization is true) => for internal use
     * @return mixed|false Cached data
     */
    public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        if (!$this->_options['caching'])
        {
            return false;
        }
        $id = $this->_id($id); // cache id may need prefix
        $this->_lastId = $id;
        self::_validateIdOrTag($id);
        $data = $this->_backend->load($id, $doNotTestCacheValidity);
        if ($data === false)
        {
            // no cache available
            return false;
        }
        return $data;
    }
    
    /**
     * Save some data in a cache
     *
     * @param  mixed $data           Data to put in cache (can be another type than string if automatic_serialization is on)
     * @param  string $id             Cache id (if not set, the last cache id will be used)
     * @param  array $tags           Cache tags
     * @param  int $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @throws Zend_Cache_Exception
     * @return boolean True if no problem
     */
    public function save($data, $id = null, $tags = array(), $specificLifetime = false, $priority = 8)
    {
        if (!$this->_options['caching'])
        {
            return true;
        }
        if ($id === null)
        {
            $id = $this->_lastId;
        }
        else
        {
            $id = $this->_id($id);
        }
        self::_validateIdOrTag($id);
        
        if (is_resource($data))
        {
            Zend_Cache::throwException('Data can\'t be a resource as it can\'t be serialized');
        }
        
        /*if ($this->_options['ignore_user_abort'])
        {
            $abort = ignore_user_abort(true);
        }*/
        $result = $this->_backend->save($data['data'], $id, $tags, $specificLifetime);
        /*if ($this->_options['ignore_user_abort'])
        {
            ignore_user_abort($abort);
        }*/
        if (!$result)
        {
            // maybe the cache is corrupted, so we remove it !
            if ($this->_options['logging'])
            {
                $this->_log(__CLASS__.'::'.__METHOD__.'() : impossible to save cache (id='.$id.')');
            }
            $this->remove($id);
            return false;
        }
        return true;
    }
}