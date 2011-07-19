<?php

class Error_Controller_Error extends Glitch_Controller_Action_RestError
{

    /**#@+
     * HTTP status codes
     *
     * @var int
     */
    const NOT_FOUND = 404;
    const SERVICE_UNAVAILABLE = 503;
    const FORBIDDEN = 403;
    /**#@-*/

    /**
     * Initializes the controller
     *
     * @return void
     */
    public function init()
    {
        // Disable the regular layout rendering: the application
        // has multiple modules, which all share this error controller.
        // Therefore, we ought not depend upon the modules' individual
        // layouts -- this may result in broken HTML.

        //$this->_helper->layout->disableLayout();
        $this->view->bodyTitle = 'Er is een fout opgetreden';
        $this->view->bodyId    = 'error';
    }

    /**
     * Default incoming action
     * // @TODO: Add exception handler for Idm_Rest_Controller_Exception
     *
     * @return void
     * @throws Exception
     */
    public function errorAction()
    {
        $error = $this->_getParam('error_handler');
        if (null === $error) {
            $this->_helper->viewRenderer->setNoRender();
            return; // No error occurred
        }

        // Figure out the type of error we're dealing with
        $code = self::SERVICE_UNAVAILABLE;

        // Application error or a missing page?
        if (Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER == $error->type ||
            Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION == $error->type ||
            Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE == $error->type)
        {
            $code = self::NOT_FOUND;
        }

        // Acl error?
        if (self::FORBIDDEN == $error->exception->getCode()) {
            $code = self::FORBIDDEN;
        }

        // Take action based on code
        switch ($code)
        {
            case self::NOT_FOUND:

                // Only log
                Glitch_Registry::getLog()->debug('Page not found');
                break;
            case self::FORBIDDEN:

                break;
            default:
                // Also log the HTTP status code in order to detect webservice errors
                $message = sprintf(
                    'Code: %s; Message: %s; Exception: %s;',
                    $code,
                    $error->exception->getMessage(),
                    $error->exception->getTraceAsString());
                Glitch_Registry::getLog()->err($message);
                break;
        }

        // If error occured in CLI mode, get out now
        if ('cli' == PHP_SAPI) {
            // Re-throwing exception enables caller to catch it and take action
            throw $error->exception;
        }

        // Since we're called on error, rendering may have begun. Clean it.
        $this->_response->clearBody();

        // Send the appropriate status code
        $this->_response->setHttpResponseCode($code);

        // Don't output the HTML error page if we're not rendering HTML
        if (!$this->_helper->response->isHtml()) {
            $this->_helper->viewRenderer->setNoRender();
        }

        // Conditionally show the error
        $this->view->code = $code;
        $this->view->message = $error->exception->getMessage();
        if ($this->getInvokeArg('displayExceptions')) {
            $this->view->message = $error->exception;
        }
    }

}
