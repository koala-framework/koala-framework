<?php
class Kwf_Controller_Action_Error_ErrorController extends Kwf_Controller_Action
{
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
        if ($prefix == 'json' &&
            ($isXmlHttpRequest || $errors->exception instanceof Kwf_Exception_Client)) {
            $this->_forward('json-error');
        } else {
            throw $errors->exception; // wird von Kwf_Debug::handleException behandelt
        }
    }

    public function jsonErrorAction()
    {
        $errors = $this->getRequest()->getParam('error_handler');
        $exception = $errors->exception;
        if ($exception instanceof Kwf_Exception_Client) {
            $this->view->error = $exception->getMessage();
        } else {
            if (!$exception instanceof Kwf_ExceptionNoMail) {
                $exception = new Kwf_Exception_Other($exception);
            }
            if (Kwf_Exception::isDebug()) {
                $this->view->exception = $exception->getException()->__toString();
            } else {
                $this->view->error = trlKwf('An error has occurred. Please try again later.');
            }
        }
        if ($exception instanceof Kwf_Exception_Abstract) $exception->log();
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

    public function jsonTimeoutAction()
    {
        throw new Kwf_Exception("exception");
        sleep(50);
    }

    public function jsonWrongVersionAction()
    {
        $this->view->wrongversion = true;
        $this->view->success = false;
        $l = new Kwf_Assets_Loader();
        $this->view->maxAssetsMTime = $l->getDependencies()->getMaxFileMTime();
    }
}
