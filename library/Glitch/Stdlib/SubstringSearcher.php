<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Dolf Schimmel - Freeaqingme (dolfschimmel@gmail.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Glitch_Stdlib
 */

namespace Glitch\Stdlib;

/**
 * @category   Glitch
 * @package    Glitch_Stdlib
 */
class SubstringSearcher
{

    public static function searchArray($array, $value, $directOnly = true, $separator = '\\')
    {
        $value .= $separator;
        $length = strlen($value);
        $filtered = array();
//        $cNames = $this->getCanonicalNames();
        array_walk($array, function ($value, $key) use (&$filtered, $value, $directOnly, $length, $separator)
        {
            if (substr($key, 0, $length) != $value) {
                return;
            }

            $strrpos = strrpos($key, $separator);
            if (!$directOnly || !$strrpos  || $strrpos <= $length-1) {
                $filtered[$key] = $value;
            }
        });

        return array_keys($filtered);
    }
}
