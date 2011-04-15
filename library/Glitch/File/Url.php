<?php
/**
 * Glitch
 *
 * This source file is proprietary and protected by international
 * copyright and trade secret laws. No part of this source file may
 * be reproduced, copied, adapted, modified, distributed, transferred,
 * translated, disclosed, displayed or otherwise used by anyone in any
 * form or by any means without the express written authorization of
 * 4worx software innovators BV (www.4worx.com)
 *
 * @category    Glitch
 * @package     Glitch_File
 * @subpackage  Glitch_File_Url
 * @author      4worx <info@4worx.com>
 * @copyright   2009, 4worx
 * @version     $Id$
 */

/**
 * Class to read CSV files per line returned as an associative array
 *
 * @category    Glitch
 * @package     Glitch_File
 * @subpackage  Glitch_File_Url
 */
class Glitch_File_Url extends SplFileObject
{
	public function __construct($filename, $mode = 'r', $use_include_path = false, $context = null)
	{
		parent::__construct($filename);

		$this->rewind();
	}

    /**
     * Fetch the current line as an associative array
     *
     * @return array
     */
    public function current()
    {
    	$data = trim(parent::current());
        $parts = parse_url($data);
        if (isset($parts['query']))
        {
        	$parts['query'] = $this->_parseQuery($parts['query']);
        }
        return $parts;
    }

    /**
     * Parse the querystring into an array
     *
     * @param string $queryString
     * @return array
     */
    private function _parseQuery($queryString)
    {
        $parts = html_entity_decode($queryString);
        $parts = explode('&', $parts);
        $arr = array();

        foreach($parts as $part)
        {
            $x = explode('=', $part);

            if (preg_match('~(\w+)\[(.*)\]~', $x[0], $matches))
            {
            	if ($matches[2] != '')
            	{
            	    $arr[$matches[1]][$matches[2]]= $x[1];
            	}
            	else
            	{
            		$arr[$matches[1]][]= $x[1];
            	}
            }
            else
            {
            	if (isset($x[0], $x[1]))
                {
                	// check if a parameter is used multiple times in the query string
                	if (isset($arr[$x[0]]) && is_array($arr[$x[0]]))
                	{
                		$arr[$x[0]][] = $x[1];
                	}
                	// if a parameter is used for the second time, change it to an array format
                	else if (array_key_exists($x[0], $arr))
                	{
                		$originalValue = $arr[$x[0]];
                		$arr[$x[0]] = array();
                		$arr[$x[0]][] = $originalValue;
                		$arr[$x[0]][] = $x[1];
                	}
                	else
                	{
                        $arr[$x[0]] = $x[1];
                	}
            	}
            }
        }
        unset($parts, $x, $part);
        return $arr;
    }
}