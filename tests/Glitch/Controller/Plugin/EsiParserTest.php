<?php
class Glitch_Controller_Plugin_EsiParserTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        //@todo move somewhere else, this is not correct
        define('GLITCH_CACHES_PATH', '');
        $front = array(
            'caching' => true,
            'automatic_serialization' => true,
        );
        $back = array(
            //'cache_dir' => self::ESI_CACHE_PATH,
            'cache_file_umask' => 0644,
            'file_name_prefix' => md5(__FILE__),
            'hashed_directory_level' => 1,
            'hashed_directory_umask' => 0755,
            'customBackendNaming' => true
        );
        $cache = Zend_Cache::factory('Core', new Zend_Cache_Backend_BlackHole, $front, $back);
        $_SERVER['HTTP_HOST'] = 'test.dev';
        Glitch_Controller_Plugin_TestEsiParser::setCache($cache);
        $this->_parser = new Glitch_Controller_Plugin_TestEsiParser();
        $this->_testEsi = '<esi:include src="/snippets/test" />';
    }

    public function testReplaceWithoutServerHttpHost()
    {
        unset($_SERVER['HTTP_HOST']);
        $this->assertNull($this->_parser->replace($this->_testEsi));
    }
}

class Glitch_Controller_Plugin_TestEsiParser extends Glitch_Controller_Plugin_EsiParser
{
    public function replace($url)
    {
        return $this->_replaceEsiInclude($url);
    }
}
