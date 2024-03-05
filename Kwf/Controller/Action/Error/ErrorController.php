<?php
class Kwf_Controller_Action_Error_ErrorController extends Kwf_Controller_Action
{
    protected function _isAllowedResource()
    {
        return true;
    }

    protected function _validateCsrf()
    {
    }

    public function preDispatch()
    {
    }

    public function errorAction()
    {
        $errors = $this->getRequest()->getParam('error_handler');
        if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER ||
            $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION)
        {
            $errors->exception = new Kwf_Exception_NotFound();
        }

        $prefix = substr($this->_getParam('action'), 0, 4);
        $isXmlHttpRequest =
            (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']== 'POST') ||
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        if ($this->_getParam('jsonOutput')) {
            $this->_forward('json-error');
        } else {
            throw $errors->exception; // wird von Kwf_Debug::handleException behandelt
        }
    }

    public function jsonErrorAction()
    {
        $errors = $this->getRequest()->getParam('error_handler');
        $exception = $errors->exception;
        if ($exception instanceof Kwf_Exception_Abstract) {
            $this->getResponse()->setRawHeader($exception->getHeader());
        } else {
            $this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Server Error');
        }
        if ($exception instanceof Kwf_Exception_Client) {
            $this->view->error = $exception->getMessage();
        } else {
            if (!$exception instanceof Kwf_Exception_Abstract) {
                $exception = new Kwf_Exception_Other($exception);
            }
            $this->view->error = $exception->getMessage();
            if (!$this->view->error) $this->view->error = 'An error occurred';
            if (Kwf_Exception::isDebug()) {
                $this->view->exception = explode("\n", $exception->getException()->__toString());
            }
        }
        $exception->log();
    }

    public function jsonMailAction()
    {
        if ($this->_getParam('message')) {
            $e = new Kwf_Exception_JavaScript($this->_getParam('message'));
            $e->setUrl($this->_getParam('url'));
            $e->setLineNumber($this->_getParam('lineNumber'));
            $e->setLocation($this->_getParam('location'));
            $e->setReferrer($this->_getParam('referrer'));

            try {
                $stack = Zend_Json::decode($this->_getParam('stack'));
                $e->setStack($stack);
            } catch (Exception $e) {
            }
            $e->log();
        }
    }
}
