<?php
class Vps_Controller_Action_Error_ErrorController extends Vps_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->getRequest()->getParam('error_handler');
        if ($errors->exception && !($errors->exception instanceof Vps_ClientException)
             && class_exists('FirePHP') && FirePHP::getInstance()) {
            //throw $errors->exception;
            FirePHP::getInstance()->fb($errors->exception);
        }

        if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER ||
            $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION)
        {
            $errors->exception = new Vps_Exception_NotFound();
        }

        $prefix = substr($this->_getParam('action'), 0, 4);
        $isXmlHttpRequest =
            (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']== 'POST') ||
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        if ($prefix == 'json' &&
            ($isXmlHttpRequest || $errors->exception instanceof Vps_Exception_Client)) {
            $this->_forward('json-error');
        } else {
            throw $errors->exception; // wird von Vps_Debug::handleException behandelt
        }
    }

    public function jsonErrorAction()
    {
        $errors = $this->getRequest()->getParam('error_handler');
        $exception = $errors->exception;
        if ($exception instanceof Vps_Exception_Client) {
            $this->view->error = $exception->getMessage();
        } else {
            if (!$exception instanceof Vps_ExceptionNoMail) {
                $exception = new Vps_Exception_Other($exception);
            }
            if (Vps_Exception::isDebug()) {
                $this->view->exception = $exception->getException()->__toString();
            } else {
                $this->view->error = trlVps('An error has occurred. Please try again later.');
            }
        }
        if ($exception instanceof Vps_Exception_Abstract) $exception->log();
    }

    public function jsonMailAction()
    {
        throw new Vps_Exception_JavaScript($this->_getParam('msg'));
    }

    public function jsonTimeoutAction()
    {
        throw new Vps_Exception("exception");
        sleep(50);
    }

    public function jsonWrongVersionAction()
    {
        $this->view->wrongversion = true;
        $this->view->success = false;
    }
}
