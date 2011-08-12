<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Log.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
// require_once 'Zend/Application/Resource/ResourceAbstract.php';


/**
 * Resource for initializing the locale
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Glitch_Application_Resource_Log
    extends Zend_Application_Resource_Log
{
    const DEFAULT_LOGNAME = 'main';

    /**
     * @var array
     */
    protected $_log = array();

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Log
     */
    public function init()
    {
        $log = $this->getLog();
        Glitch_Registry::setLog($log);
        return $log;
    }

    /**
     * Attach logger
     *
     * @param  Zend_Log $log
     * @return Zend_Application_Resource_Log
     */
    public function setLog(Zend_Log $log, $name = self::DEFAULT_LOGNAME)
    {
        $this->_log[$name] = $log;
        return $this;
    }

    public function getLog($name = null)
    {
        if (null === $name) {
            $name = self::DEFAULT_LOGNAME;
        }

        if (count($this->_log) == 0) {
            $this->_initLog();
        }

        if ( !isset($this->_log[$name])) {
            throw new Glitch_Application_Exception_RuntimeException(sprintf(
                'A log instance with name "%s" was tried to retrieve but wasn\'t set.', $name)
            );
        }

        return $this->_log[$name];
    }

    protected function _initLog()
    {
        $options = $this->getOptions();
        if(isset($options['enabled']) && false == $options['enabled']) {
            return;
        }

        foreach ($options['writers'] as $name => $logOptions) {
            if (!is_array($logOptions)) {
                continue;
            }

            $this->_log[$name] = $this->_initInstance($logOptions);
        }

    }

    protected function _initInstance(array $options)
    {
        $logger = new Zend_Log();

        foreach ($options as $option)
        {
            $writerClass = $this->_getActorClassName('Writer', $option['writerName']);

            $writerParams = array();
            if(isset($option['writerParams'])) {
                $writerParams = $option['writerParams'];
            }

            $writer = new $writerClass($writerParams);
            $logger->addWriter($writer);

            if (isset($option['formatter']) && is_array($option['formatter'])) {
                // @todo (unit)test
                $formatterClass = $this->_getActorClassName('Formatter', $option['formatter']['name']);
                $formatter = new $formatterClass($option['formatter']['options']);
                $writer->setFormatter($formatter);
            }

            if (isset($option['filters']) && is_array($option['filters'])) {
                // @todo (unti)test
                foreach($option['filters'] as $filterOptions) {
                    $filterClass = $this->_getActorClassName('Filter', $filterOptions['name']);
                    $filter = new $filterClass($filterOptions['options']);
                    $writer->addFilter($filter);
                }
            }

        }

        return $logger;
    }

    protected function _getActorClassName($type, $name)
    {
        $name = ucfirst($name);
        if (Zend_Loader_Autoloader::autoload($name)) {
            return $name;
        } elseif (Zend_Loader_Autoloader::autoload('Glitch_Log_' . $type . '_' . $name)) {
            return 'Glitch_Log_' . $type . '_' . $name;
        } elseif (Zend_Loader_Autoloader::autoload('Zend_Log_' . $type . '_' . $name)) {
            return 'Zend_Log_' . $type . '_' . $name;
        }

        throw new Glitch_Application_Exception_RuntimeException(sprintf(
            'A Log %s Class with name %s was tried to retrieve but coult not be found.',
            $type, $name)
        );
    }

}
