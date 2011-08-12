<?php
/**
 * Memcached Zend Framework implementation of libmemcached pecl (http://pecl.php.net/memcached)
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
class Glitch_Cache_Backend_Memcached extends Zend_Cache_Backend implements Zend_Cache_Backend_Interface
{
    /**
     * Default Values
     */
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT =  11211;
    const DEFAULT_WEIGHT  = 1;
    const DEFAULT_PERSISTENT = 'mc_pool';

    /**
     * Available options
     *
     * =====> (array) servers :
     * an array of memcached server ; each memcached server is described by an associative array :
     * 'host' => (string) : the name of the memcached server
     * 'port' => (int) : the port of the memcached server
     * 'weight' => (int) : number of buckets to create for this server which in turn control its
     *                     probability of it being selected. The probability is relative to the total
     *                     weight of all servers.
     *
     * @var array available options
     */
    protected $_options = array(
        'servers' => array(array(
            'host' => self::DEFAULT_HOST,
            'port' => self::DEFAULT_PORT,
            'weight'  => self::DEFAULT_WEIGHT,
        )),
        'igbinary' => false,
        'json' => false,
        'persistent' => self::DEFAULT_PERSISTENT,
        'prefix' => '',
        'compression' => true,
    );

    /**
     * Memcached object
     *
     * @var mixed memcached object
     */
    protected $_memcached = null;

    /**
     * Identifier for persistent memcached connections
     *
     * @var string
     */
    protected $_persistent_id = self::DEFAULT_PERSISTENT;

    /**
     * Constructor
     *
     * @param array $options associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct(array $options = array())
    {
        if (!extension_loaded('memcached'))
        {
            Zend_Cache::throwException('The memcached extension must be loaded for using this backend !');
        }
        parent::__construct($options);
        if (isset($this->_options['servers']))
        {
            $value= $this->_options['servers'];
            if (isset($value['host']))
            {
                // in this case, $value seems to be a simple associative array (one server only)
                $value = array(0 => $value); // let's transform it into a classical array of associative arrays
            }
            $this->setOption('servers', $value);
        }

        if (array_key_exists('persistent', $this->_options) && is_string($this->_options['persistent']))
        {
            $this->_persistent_id = $this->createPersistentKey($this->_options['persistent'], $this->_options);
        }

        /*
         * disabled the persistent connection for now, because it leads to unexpected results in
         * hits & misses of the cache
         */
        $this->_memcached = new Memcached(); //$this->_persistent_id);
        $this->_memcached->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
        $this->_memcached->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);

        /**
         * Use the igbinary serialization protocol if compiled in pecl memcached module
         */
        if (isset($this->_options['json']) && ($this->_options['json'] === true) &&
            $this->_memcached->getOption(Memcached::HAVE_JSON)
        )
        {
            $this->_memcached->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_JSON);
        }
        /**
         * Use the igbinary serialization protocol if compiled in pecl memcached module
         */
        else if (isset($this->_options['igbinary']) && ($this->_options['igbinary'] === true) &&
            $this->_memcached->getOption(Memcached::HAVE_IGBINARY)
        )
        {
            $this->_memcached->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
        }
        if (isset($this->_options['prefix']) && ($this->_options['prefix'] != ''))
        {
            $this->_memcached->setOption(Memcached::OPT_PREFIX_KEY, $this->_options['prefix']);
        }
        if (isset($this->_options['compression']) && is_bool($this->_options['compression']))
        {
            $this->_memcached->setOption(Memcached::OPT_COMPRESSION, $this->_options['compression']);
        }

        /**
         * When using persistent connections, do not re-add the servers and options
         */
        if (!count($this->_memcached->getServerList()))
        {
            // check servers array for options that are set
            foreach ($this->_options['servers'] as &$server)
            {
                if (!array_key_exists('port', $server))
                {
                    $server['port'] = self::DEFAULT_PORT;
                }
                if (!array_key_exists('weight', $server))
                {
                    $server['weight'] = self::DEFAULT_WEIGHT;
                }
                $this->_memcached->addServer($server['host'], $server['port'], $server['weight']);
            }
            //$this->_memcached->addServers($this->_options['servers']);
        }
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @return string|false cached datas
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        $tmp = $this->_memcached->get($id);
        if ($this->_memcached->getResultCode() === Memcached::RES_SUCCESS)
        {
        //if (is_array($tmp)) {
            //return $tmp[0];
            return $tmp;
        }
        return false;
    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param  string $id Cache id
     * @return mixed|false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        $tmp = $this->_memcached->get($id);
        if ($this->_memcached->getResultCode() === Memcached::RES_SUCCESS)
        {
        //if (is_array($tmp)) {
            //return $tmp[1];
            return true;
        }
        return false;
    }

    /**
     * Save some string data into a cache record
     *
     * Note : $data is always "string" (serialization is done by the
     * core not by the backend)
     *
     * @param  string $data             Datas to cache
     * @param  string $id               Cache id
     * @param  array  $tags             Array of strings, the cache record will be tagged by each string entry
     * @param  int    $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @return boolean True if no problem
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $lifetime = $this->getLifetime($specificLifetime);
        //if (!($result = $this->_memcached->add($id, array($data, time(), $lifetime), $lifetime))) {
            //$result = $this->_memcached->set($id, array($data, time(), $lifetime), $lifetime);
            $result = $this->_memcached->set($id, $data, $lifetime);
            if ($this->_memcached->getResultCode() == Memcached::RES_ERRNO)
            {
                /*
                 * start error reporting
                 */
                $server = $this->_memcached->getServerByKey($id);
                $msg = sprintf('Memcached server %s:%d is down!!', $server['host'], $server['port']);

                /**
                 * @TODO
                 *
                 * This try/catch construct is a temporary fix for preventing fatal PHP errors. Somehow the log line
                 * gets written to file but fwrite (in Zend_Log_Writer_Stream) returns false, which causes Zend_Log to
                 * throw an exception. An exception at this point in the code execution causes an out-of-stack error.
                 *
                 * For now the exception is catched and ignored, the only risk is missing a log line.
                 * But this issue needs further research!
                 */
                try
                {
                    Zend_Registry::get('logger')->alert($msg);
                }
                catch (Exception $e)
                {
                    // ignore
                }
            }
        //}
        return $result;
    }

    /**
     * Remove a cache record
     *
     * @param  string $id Cache id
     * @return boolean True if no problem
     */
    public function remove($id)
    {
        return $this->_memcached->delete($id);
    }

    /**
     * Clean some cache records
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => unsupported
     * 'matchingTag'    => unsupported
     * 'notMatchingTag' => unsupported
     * 'matchingAnyTag' => unsupported
     *
     * @param  string $mode Clean mode
     * @param  array  $tags Array of tags
     * @throws Zend_Cache_Exception
     * @return boolean True if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        switch ($mode)
        {
            case Zend_Cache::CLEANING_MODE_ALL:
                return $this->_memcached->flush();
                break;
            case Zend_Cache::CLEANING_MODE_OLD:
                $this->_log('Zend_Cache_Backend_Memcached::clean() : CLEANING_MODE_OLD is unsupported by the Memcached backend');
                break;
            default:
                Zend_Cache::throwException('Invalid mode for clean() method');
                break;
        }
    }

    /**
     * Set the frontend directives
     *
     * @param  array $directives Assoc of directives
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setDirectives($directives)
    {
        parent::setDirectives($directives);
        $lifetime = $this->getLifetime(false);
        if ($lifetime > 2592000)
        {
            // #ZF-3490 : For the memcached backend, there is a lifetime limit of 30 days (2592000 seconds)
            $this->_log('memcached backend has a limit of 30 days (2592000 seconds) for the lifetime');
        }
        if ($lifetime === null)
        {
            // #ZF-4614 : we tranform null to zero to get the maximal lifetime
            parent::setDirectives(array('lifetime' => 0));
        }
    }

    /**
     * Creates a persistent key
     *
     * @param string $prefix
     * @param array $options
     * @return string
     */
    private function createPersistentKey($prefix, array $options = null)
    {
        if (null !== $options)
        {
            // Filter empty options, those fields are not provided when searching from the front end
            $options = array_filter($options, create_function('$var', 'return ("" != $var);'));

            // Cast all values to strings. This prevents serialization differences, e.g.:
            // array('compression' => true) versus array('compression' => 1)
            $options = array_map('strval', $options);

            // Sort the filtered options to have a consistent key
            ksort($options);

            $prefix .= serialize($options);
        }

        $key = md5($prefix);

        return $key;
    }
}