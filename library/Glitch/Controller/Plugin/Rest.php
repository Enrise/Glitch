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
 * @package     Glitch_Controller
 * @subpackage  Plugin
 * @author      4worx <info@4worx.com>
 * @copyright   2010, 4worx
 * @version     $Id$
 */

/**
 * Plugin for handling REST request calls by setting the format based on the Accept header
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Plugin
 * @author      dpapadogiannakis@4worx.com
 */
class Glitch_Controller_Plugin_Rest extends Zend_Controller_Plugin_Abstract
{
    /**
     * Mapping of the available formats and their Accept header variants
     *
     * @var array
     */
    protected static $_mapping = array(
        'json' => 'application/json',
        'xml' => 'application/xml',
    );

    /**
     * Add multiple formats in a single go
     *
     * @param array|Zend_Config $formats
     */
    public static function addFormats($formats)
    {
        if ($formats instanceof Zend_Config)
        {
            $formats = $formats->toArray();
        }
        foreach ($formats as $format => $requestHeader)
        {
            self::addFormat($format, $requestHeader);
        }
        return null;
    }

    /**
     * Add a single format
     *
     * @param string $format
     * @param string $requestHeader
     */
    public static function addFormat($format, $requestHeader)
    {
        if (!is_scalar($format) || !is_scalar($requestHeader))
        {
            throw new InvalidArgumentException('Invalid argument given, string expected!');
        }
        self::$_mapping[$format] = $requestHeader;
    }

    /**
     * Get all the supported formats
     *
     * @return array
     */
    public static function getFormats()
    {
        return array_keys(self::$_mapping);
    }

    /**
     * Check if the request is HTTP, if so check for a Accept header and set the right format in the request object
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // Skip header check when we don't have a HTTP request (for instance: cli-scripts)
        if (! $request instanceof Zend_Controller_Request_Http) {
            return;
        }
        $this->getResponse()->setHeader('Vary', 'Accept');
        $header = $request->getHeader('Accept');
        foreach (self::$_mapping as $format => $requestHeader)
        {
            if (false !== strstr($header, $requestHeader))
            {
                $request->setParam('format', $format);
                break;
            }
        }
    }
}