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
 * Resource for initializing the logger
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Log extends Zend_Application_Resource_Log
{
    /**
     * Retrieves the logger object
     *
     * @return Zend_Log
     * @throws Glitch_Application_Resource_Exception
     */
    public function getLog()
    {
        if (null === $this->_log)
        {
            $options = $this->getOptions();

            // Force these options to be set - don't rely on the defaults!
            if (!isset($options['level']))
            {
                throw new Glitch_Application_Resource_Exception('Undefined log option: "level"');
            }

            // Validate the log level
            $level = constant('Zend_Log::' . $options['level']);
            if (null === $level)
            {
                throw new Glitch_Application_Resource_Exception('Unknown log level: "' . $options['level'] . '"');
            }

            // Ensure the request is initialized
            $this->_bootstrap->bootstrap('Request');
            $request = $this->_bootstrap->getResource('Request');
            $isHttpRequest = ($request instanceof Zend_Controller_Request_Http);

            // Use localhost as name if not running in HTTP mode
            $host = ($isHttpRequest) ? $request->getHttpHost() : 'localhost';
            if (strncasecmp($host, 'www.', 4) == 0)
            {
                $host = substr($host, 4); // Remove "www." prefix for readability
            }

            $this->_log = new Zend_Log();

            // Build filename, e.g. "20090601_localhost.log"
            $file = Zend_Date::now()->toString('yyyyMMdd') . '_' . $host . '.log';
            $file = GLITCH_LOGS_PATH . DIRECTORY_SEPARATOR . $file;

            $writer = new Zend_Log_Writer_Stream($file);

            // Use custom logging format, e.g.
            // [2010-08-07T17:03:18+02:00] ERR (/account/login): Method "_getParams" does not exist
            $format = '[%timestamp%] %priorityName%';
            if ($isHttpRequest)
            {
                $format .= ' (%requestUri%)';
                $this->_log->setEventItem('requestUri', $request->getRequestUri());
            }
            $format .= ': %message%';

            $formatter = new Zend_Log_Formatter_Simple($format . PHP_EOL);
            $writer->setFormatter($formatter);
            $this->_log->addWriter($writer);

            // Also send log output to browser console?
            if ($isHttpRequest && (isset($options['toFirebug']) && $options['toFirebug']))
            {
                $this->_log->addWriter(new Zend_Log_Writer_Firebug());
            }

            $filter = new Zend_Log_Filter_Priority($level);
            $this->_log->addFilter($filter);

            // Allow application-wide access
            Glitch_Registry::setLog($this->_log);
        }
        return $this->_log;
    }
}