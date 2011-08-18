<?php
/**
 * Simplified Zend Shared Memory Zend Framework implementation
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
class Glitch_Cache_Backend_ZendShMem extends Zend_Cache_Backend implements Zend_Cache_Backend_Interface
{
    /**
     * Available options
     *
     * =====> (string) namespace :
     * Namespace to be used for chaching operations
     *
     * @var array available options
     */
    protected $_options = array(
        'namespace' => 'glitch'
    );

    /**
     * Constructor
     *
     * @param  array $options associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct(array $options = array())
    {
        if (!function_exists('zend_shm_cache_store'))
        {
            Zend_Cache::throwException('Glitch_Cache_Backend_ZendShMem backend has to be used within Zend Server / Zend Platform environment.');
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
        $tmp = zend_shm_cache_fetch($this->_options['namespace'] . '::' . $id);
        if ($tmp !== null && $tmp !== false)
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
        $tmp = zend_shm_cache_fetch($this->_options['namespace'] . '::' . $id);
        if ($tmp !== null && $tmp !== false)
        {
            return true;
        }
        return false;
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
        if (zend_shm_cache_store($this->_options['namespace'] . '::' . $id,
                                  $data,
                                  $lifetime) === false) {

            return false;
        }
        return true;
    }

    /**
     * Remove a cache record
     *
     * @param  string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id)
    {
        return zend_shm_cache_delete($this->_options['namespace'] . '::' . $id);
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
                return zend_shm_cache_clear($this->_options['namespace']);
                break;
            case Zend_Cache::CLEANING_MODE_OLD:
                $this->_log('Glitch_Cache_Backend_ZendShMem::clean() : CLEANING_MODE_OLD is unsupported by the ZendShMem backend');
                break;
            default:
                Zend_Cache::throwException('Invalid mode for clean() method');
                break;
        }
    }
}
