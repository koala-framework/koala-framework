<?php
class Vps_Controller_Action_Error_ErrorController extends Vps_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->getRequest()->error_handler;

        if ($this->_getParam('module') == 'component' &&
            $this->_getParam('action') == 'jsonIndex' &&
            $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER) {
            $errors->exception = new Vps_ComponentNotFoundException();
        }

        $prefix = substr($this->_getParam('action'), 0, 4);
        $isHttpRequest = $_SERVER['REQUEST_METHOD'] == 'POST' ||
                    isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        if (($prefix == 'ajax' || $prefix == 'json') &&
            ($isHttpRequest || $errors->exception instanceof Vps_ClientException)) {
            $this->_forward('jsonError');
        } else {

            if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER ||
                $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION ||
                $errors->exception instanceof Vps_Controller_Action_Web_FileNotFoundException) {
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                $file = 'Error404.html';
            //auskommentiert - wenn ich einen exception bekomm dann will ich eine gscheite :D
            //} else if ($errors->exception instanceof Vps_Controller_Exception) {
            //    $file = 'ErrorVpc.html';
            } else {
                $file = 'Error.html';
            }

            $this->view->setRenderFile($file);

            $config = Zend_Registry::get('config');
            $this->view->type = $errors->type;
            $this->view->exception = $errors->exception;
            $this->view->message = $errors->exception->getMessage();
            $this->view->requestUri = $_SERVER['REQUEST_URI'];
            if ($config->debug->errormail != '') {
                Vps_Debug::sendErrorMail($errors->exception, $config->debug->errormail);
                $this->view->debug = false;
            } else {
                $this->view->debug = true;
            }
        }
    }

    public function jsonErrorAction()
    {
        $errors = $this->getRequest()->error_handler;
        $exception = $errors->exception;
        if ($exception instanceof Vps_ClientException) {
            $this->view->error = $exception->getMessage();
        } else if ($exception instanceof Vps_ComponentNotFoundException) {
            $this->view->success = true;
            $this->view->failure = 'foo';
        } else {
            $config = Zend_Registry::get('config');
            if ($config->debug->errormail != '') {
                Vps_Debug::sendErrorMail($exception, $config->debug->errormail);
                $this->view->error = 'An error occured. Please try again later.';
            } else {
                $this->view->exception = $exception->__toString();
            }
        }
    }

    public function jsonMailAction()
    {
        $exception = new Vps_JavaScriptException($this->_getParam('msg'));
        Vps_Debug::sendErrorMail($exception, Zend_Registry::get('config')->debug->errormail);
    }

    public function jsonWrongVersionAction()
    {
        $this->view->wrongversion = true;
        $this->view->success = false;
    }
}
