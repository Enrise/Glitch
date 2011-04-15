<?php
/**
 * Custom Zend framework class caching functionality implementation 
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
 * @see         Zend_Cache_Frontend_Class
 */
class Glitch_Cache_Frontend_Class extends Zend_Cache_Frontend_Class
{
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
        $result = $this->_backend->save($data, $id, $tags, $specificLifetime);
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