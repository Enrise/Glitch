<?php
/**
 * Glitch
 *
 * Copyright (c) 2010, 4worx BV (www.4worx.com).
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of 4worx nor the names of his contributors
 *     may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Plugin
 * @author      Jeroen van Dijk <jeroen@4worx.com>
 * @copyright   2010, 4worx
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @version     $Id$
 */

/**
 * ESI compatible parser to be able to develop site without ESI enabled
 * webserver
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Plugin
 */
class Glitch_Controller_Plugin_EsiParser extends Zend_Controller_Plugin_Abstract
{
	/**#@+
	 * Class constants
	 *
	 * @var string
	 */
	const ESI_INCLUDE_REGEX = '~<esi:include src="(.+?)"[ ]*/>~s';
	const ESI_CACHE_REGEX = '~Cache-Control: max-age=(\d+)~';
	const ESI_CACHE_PATH = GLITCH_CACHES_PATH; //adept to own needs
	const ESI_CACHEKEY_PREFIX = 'esi_';
	const ESI_DATA_HASH_CHECK = 'crc32';
    /**#@-*/

	/**
     * Cache object
     *
     * @var Zend_Cache_Core
     */
    protected static $_cache;

    /**
     * Enable or desable the cache by EsiParser instance
     *
     * @var boolean
     */
    protected static $_cacheEnabled = true;

    /**
     * A context that will be used to fetch the http request needed for ESI
     *
     * @var resource
     */
    protected $_httpContext = null;

	/**
	 * On end of dispatchLoop check for esi:include tags
	 *
	 * @return void
	 */
    public function dispatchLoopShutdown()
    {
    	if (null === self::$_cache)
    	{
    		self::setCache();
    	}

    	$data = $this->getResponse()->getBody();
    	$data = $this->_replaceEsiIncludes($data);
    	$this->getResponse()->setBody($data);
    }

    /**
     * Sets the locale cache
     *
     * This is a separate, public static method, thereby allowing
     * externally setting the cache. If no Zend_Cache_Core object
     * is given, the code will try to setup a file-based caching.
     *
     * @return void
     */
    public static function setCache(Zend_Cache_Core $cache = null)
    {
        if (null === $cache)
        {
            $front = array(
                'caching' => self::$_cacheEnabled,
                'automatic_serialization' => true,
            );
            $back = array(
                'cache_dir' => self::ESI_CACHE_PATH,
                'cache_file_umask' => 0644,
                'file_name_prefix' => md5(__FILE__),
                'hashed_directory_level' => 1,
                'hashed_directory_umask' => 0755,
            );
            $cache = Zend_Cache::factory('Core', 'File', $front, $back);
        }
        self::$_cache = $cache;
    }

    /**
     * Enables/Disables the cache for this instance
     *
     * @param bool $enable
     * @return void
     */
    public static function setCacheEnabled($enable)
    {
        self::$_cacheEnabled = (bool)$enable;
    }

    /**
     * Fetch the stream context, create if it hasn't been set before
     *
     * @return resource
     */
    protected function _getHttpContext()
    {
    	if (null === $this->_httpContext)
    	{
	    	$this->_httpContext = stream_context_create(
	    	    array('http' =>
	    	        array(
	    	            'ignore_errors' => true, // only available since php 5.2.10
	    	        )
	    	    )
	        );
    	}
    	return $this->_httpContext;
    }

    /**
     * Replace esi includes
     *
     * @param string $data
     * @return string
     */
    protected function _replaceEsiIncludes($data)
    {
        $hash = hash(self::ESI_DATA_HASH_CHECK, $data);
        $esiTags = array();
        if (preg_match_all(self::ESI_INCLUDE_REGEX, $data, $esiTags, PREG_SET_ORDER) > 0)
        {
            foreach($esiTags as $esiTag)
            {
            	$content = $this->_replaceEsiInclude($esiTag[1]);
                $data = str_replace($esiTag[0], $content, $data);
            }
        }

        if ($hash != hash(self::ESI_DATA_HASH_CHECK, $data))
        {
        	return $this->_replaceEsiIncludes($data);
        }
        return $data;
    }

    /**
     * Replace one found esi include with a given url
     *
     * @param string $url
     * @return string|null
     */
    protected function _replaceEsiInclude($url)
    {
    	$uri = 'http://'.$_SERVER['HTTP_HOST'] . $url;
    	$key = $this->_getCacheId($uri);
    	if ($this->_cacheEnabled())
    	{
    		// detect if url is cached
            $data = self::$_cache->load($key);
            if (false !== $data)
            {
            	return $data;
            }
    	}

    	$fp = fopen($uri, 'r', false, $this->_getHttpContext());
    	if (false !== $fp)
    	{
	        $data = stream_get_contents($fp);
	        if ($this->_cacheEnabled())
	        {
	        	$meta = stream_get_meta_data($fp); // fetch the metadata of the fopen call
		        foreach ($meta['wrapper_data'] as $header)
		        {
		        	$match = array();
		        	if (preg_match(self::ESI_CACHE_REGEX, $header, $match))
		        	{
		                if (false !== $data)
		                {
		                	// cache url with the respected max-age setting
		                	self::$_cache->save($data, $key, array(), intval($match[1]));
		                	break;
		                }
		        	}
		        }
	        }
	        fclose($fp);
	        return $data;
    	}
    	return null;
    }

    /**
     * Make an id for the cache
     *
     * @param string
     * @return string
     */
    protected function _getCacheId($uri = null)
    {
        return self::ESI_CACHEKEY_PREFIX . md5($uri);
    }

    /**
     * Tells if there is an active cache object and if the cache has not been disabled
     *
     * @return bool
     */
    private function _cacheEnabled()
    {
        return ((self::$_cache !== null) && self::$_cacheEnabled);
    }
}