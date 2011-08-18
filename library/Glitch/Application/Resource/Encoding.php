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
 * @package     Glitch_Application
 * @subpackage  Resource
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Resource for setting the encoding
 *
 * This resource assumes the application is using UTF-8. Although you're free to set any
 * other encoding, it is highly encouraged to stick to the default. Also, make sure your storage
 * providers, such as a database, are using the same character set.
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Encoding extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Encoding string, e.g. "utf-8"
     *
     * @var string
     */
    protected $_encoding = null;

    /**
     * Initializes this resource
     *
     * @return string
     */
    public function init()
    {
        return $this->getEncoding();
    }

    /**
     * Retrieves the encoding
     *
     * @return string
     * @throws Glitch_Application_Resource_Exception
     */
    public function getEncoding()
    {
        if (null === $this->_encoding)
        {
            // Test for UTF-8 support. The regex will wrongly match 4 chars,
            // rather than 2, if PCRE was compiled without UTF-8 support. If the chars
            // in preg_match appear garbled, change the encoding of this file to UTF-8!
            if (!extension_loaded('iconv') || !preg_match('~^..$~u', 'Â±'))
            {
                throw new Glitch_Application_Resource_Exception('No multibyte strings support');
            }

            $options = $this->getOptions();

            // Force these options to be set - don't rely on the defaults!
            if (!isset($options['encoding']))
            {
                throw new Glitch_Application_Resource_Exception('Undefined encoding option: "encoding"');
            }

            $this->_encoding = (string) $options['encoding'];

            // Override the default charset; also sends the appropriate HTTP header
            ini_set('default_charset', $this->_encoding);

            // ZF uses iconv for e.g. form validation and Zend_Locale
            iconv_set_encoding('internal_encoding', $this->_encoding);

            // MB extension is not required by ZF, so don't throw exceptions
            if (function_exists('mb_internal_encoding'))
            {
                // ZF uses this for e.g. Zend_Filter and Zend_Feed
                mb_internal_encoding($this->_encoding);
            }

            // Allow application-wide access; e.g. Zend_Mail uses this
            Glitch_Registry::setEncoding($this->_encoding);
        }

        return $this->_encoding;
    }
}