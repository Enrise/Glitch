<?php
/**
 * Action helper that helps logging of exceptions. Particularly useful for
 * the (Rest)ErrorControllers
 *
 * @category    Glitch
 * @package     Glitch_Controller
 * @subpackage  Action_Helper
 */
class Glitch_Controller_Action_Helper_ExceptionLogger
    extends Zend_Controller_Action_Helper_Abstract
{
    protected $_loggerName = Glitch_Application_Resource_Log::DEFAULT_LOGNAME;

    public function logException(exception $exception)
    {
        $logger = $this->_getLogInstance();
        $logger->log($exception, $this->_getPrio($exception));
    }

    protected function _getPrio(exception $exception)
    {
        if($exception instanceof Glitch_Exception_MessageInterface)
        {
            return 4;
        } else {
            return 2;
        }
    }

    protected function _getLogInstance()
    {
        return $this->getFrontController()
                        ->getParam('bootstrap')
                            ->getPluginResource('log')
                                ->getLog($this->_getLoggerName());
    }

    protected function _getLoggerName()
    {
        return $this->_loggerName;
    }
}
