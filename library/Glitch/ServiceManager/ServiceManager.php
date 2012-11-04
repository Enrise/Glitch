<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace Glitch\ServiceManager;

use Zend\ServiceManager\ServiceManager as ZendServiceManager;


/**
 * @category Zend
 * @package  Zend_ServiceManager
 */
class ServiceManager extends ZendServiceManager
{

    public function getSubClasses($class, $directOnly = true)
    {
        $class .= '\\';
        $length = strlen($class);
        $filtered = array();
        $cNames = $this->getCanonicalNames();
        array_walk($cNames, function ($value, $key) use (&$filtered, $class, $directOnly, $length)
        {
            if (substr($key, 0, $length) != $class) {
                return;
            }

            $strrpos = strrpos($key, '\\');
            if (!$directOnly || !$strrpos  || $strrpos <= $length-1) {
                $filtered[$key] = $value;
            }
        });

        return array_keys($filtered);
    }
}
