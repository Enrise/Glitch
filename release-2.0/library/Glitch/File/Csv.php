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
 * @subpackage  Glitch_File_Csv
 * @author      4worx <info@4worx.com>
 * @copyright   2009, 4worx
 * @version     $Id$
 */

/**
 * Class to read CSV files per line returned as an associative array
 *
 * @category    Glitch
 * @package     Glitch_File
 * @subpackage  Glitch_File_Csv
 */
class Glitch_File_Csv extends SplFileObject
{
	/**
	 * Array with keys (first line of CSV file)
	 *
	 * @var array
	 */
	private $_map;
	
	/**
	 * Delimiter to use for field recognition
	 *
	 * @var string
	 */
    private $_delimiter = ';';
	
	/**
	 * Enclosure to use for data recognition
	 *
	 * @var string
	 */
	private $_enclosure = '"';
	
	public function __construct($filename, $mode = 'r', $use_include_path = false, $context = null)
	{
		parent::__construct($filename);
		$this->setFlags(SplFileObject::READ_CSV);
		$this->setCsvControl($this->_delimiter, $this->_enclosure);
		
		$this->rewind();
		$this->_map = parent::current();
	}
	
	/**
	 * Set a different delimiter to recognize fields
	 *
	 * @param string $delimiter
	 * @return void
	 */
	public function setDelimiter($delimiter)
	{
		$this->_delimiter = $delimiter;
		$this->setCsvControl($this->_delimiter, $this->_enclosure);
	}
	
	/**
	 * Set a different enclosure to recognize data in fields
	 *
	 * @param string $enclosure
	 * @return void
	 */
    public function setEnclosure($enclosure)
    {
        $this->_enclosure = $enclosure;
        $this->setCsvControl($this->_delimiter, $this->_enclosure);
    }
    
    /**
     * Fetch the current line as an associative array use the map that was created
     * on object construction
     *
     * @return array
     */
    public function current()
    {
    	$data = parent::current();
    	return array_combine($this->_map, $data);
    }
}