<?php
abstract class Glitch_Controller_Action_RestError
    extends Glitch_Controller_Action_Rest
{
    protected $_errorMethod = 'errorAction';

    public function dispatch($request)
    {
        if($request instanceof Glitch_Controller_Request_Rest) {
            return 'restAction';
        }

        return $this->_errorMethod;
    }

    public function error()
    {
        $request = $this->getRequest();
        return $this->{$this->dispatch($request)}();
    }

    public function restAction()
    {

        $error = $this->_getParam('error_handler');
        $exception = $error->exception;

        $this->getHelper('exceptionLogger')->logException($exception);

        // Default zend_exceptions are considered 500's (actual message set
        // inside the view scripts)
        $message = '';
        $code = 500;

        if($exception instanceof Glitch_Exception_MessageInterface) {
            // Set correct message and output
            $message = $exception->getMessage();
            if($exception->getCode() != 0) {
                $code = $exception->getCode();
            }

            // Enable output
            $this->getResponse()->renderBody(true);

        } elseif ($exception instanceof Glitch_ExceptionInterface) {
            // Set correct code
            if($exception->getCode() != 0) {
                $code = $exception->getCode();
            }

            // Disable output
            $this->getResponse()->renderBody(false);
        }

        $this->getResponse()->setHttpResponseCode($code);
        return array('data' => array('message' => $message, 'code' => $code));
    }
}
