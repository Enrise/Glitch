<?php
/**
 * Simplified Apc Zend Framework implementation
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
 * @subpackage  Glitch_Cache_Backend
 * @author      4worx <info@4worx.com>
 * @copyright   2009, 4worx
 * @version     $Id$
 */

/**
 * @package     Glitch_Cache
 * @subpackage  Glitch_Cache_Backend
 * @see         Zend_Cache_Backend_Interface
 * @see         Zend_Cache_Backend
 */
class Glitch_Cache_Backend_Apc extends Zend_Cache_Backend implements Zend_Cache_Backend_Interface
{
    /**
     * Constructor
     *
     * @param  array $options associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct(array $options = array())
    {
        if (!extension_loaded('apc'))
        {
            Zend_Cache::throwException('The apc extension must be loaded for using this backend !');
        }
        parent::__construct($options);
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * WARNING $doNotTestCacheValidity=true is unsupported by the Apc backend
     *
     * @param  string  $id                     cache id
     * @param  boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string cached datas (or false)
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        $success = false;
    	$tmp = apc_fetch($id, $success);
        if (true === $success)
        {
            return $tmp;
        }
        return false;
    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param  string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        $success = false;
    	$tmp = apc_fetch($id, $success);
        return $success;
    }

    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always "string" (serialization is done by the
     * core not by the backend)
     *
     * @param string $data datas to cache
     * @param string $id cache id
     * @param array $tags array of strings, the cache record will be tagged by each string entry
     * @param int $specificLifetime if != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @return boolean true if no problem
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $lifetime = $this->getLifetime($specificLifetime);
        return apc_store($id, $data, $lifetime);
    }

    /**
     * Remove a cache record
     *
     * @param  string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id)
    {
        return apc_delete($id);
    }

    /**
     * Clean some cache records
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => unsupported
     *
     * @param  string $mode clean mode
     * @param  array  $tags array of tags
     * @throws Zend_Cache_Exception
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        switch ($mode) {
            case Zend_Cache::CLEANING_MODE_ALL:
                return apc_clear_cache('user');
                break;
            case Zend_Cache::CLEANING_MODE_OLD:
                $this->_log("Glitch_Cache_Backend_Apc::clean() : CLEANING_MODE_OLD is unsupported by the Apc backend");
                break;
            default:
                Zend_Cache::throwException('Invalid mode for clean() method');
                break;
        }
    }
}
