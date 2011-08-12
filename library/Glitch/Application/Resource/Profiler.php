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
 * Resource for initializing the profiler
 *
 * @category    Glitch
 * @package     Glitch_Application
 * @subpackage  Resource
 */
class Glitch_Application_Resource_Profiler extends Zend_Application_Resource_ResourceAbstract
{
    const REGISTRY_KEY = 'profiler';

    /**
     * Resource object
     *
     * @var Glitch_Application_Profiler
     */
    protected $_profiler = null;

    /**
     * Initializes this resource
     *
     * @return Glitch_Application_Profiler
     */
    public function init()
    {
        return $this->getProfiler();
    }

    /**
     * Retrieves the request object
     *
     * @return Zend_Controller_Request_Abstract
     */
    public function getProfiler()
    {
        if (null === $this->_profiler)
        {
            $options = $this->getOptions();

            // Force these options to be set - don't rely on the defaults!
            if (!isset($options['active']))
            {
                throw new Glitch_Exception('Undefined log option: "active"');
            }
            $enabled = (boolean)$options['active'];

            // enable or disable the profiler by ratio
            if (isset($options['ratio']))
            {
                $ratio = intval($options['ratio']);
                if (($ratio >= 0 && $ratio <= 100) && $enabled)
                {
                    $enabled = (rand(0, 100) > (100 - $ratio));
                }
            }

            // load the profiler instance
            $this->_profiler = Glitch_Application_Profiler::factory($enabled);

            // Ensure the front controller is initialized
            $this->_bootstrap->bootstrap('FrontController');

            // push the profiler on the plugin stack to time the dispatch process
            $front = $this->_bootstrap->getResource('FrontController');
            $front->registerPlugin(new Glitch_Controller_Plugin_Profiler(), 2);

            // Allow application-wide access
            Zend_Registry::set(self::REGISTRY_KEY, $this->_profiler);
        }
        return $this->_profiler;
    }
}