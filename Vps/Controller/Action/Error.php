<?php
class Vps_Controller_Action_Error extends Vps_Controller_Action
{
    public function errorAction()
    {
        $prefix = substr($this->getRequest()->getParam('action'), 0, 4);
        $isHttpRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        if (($prefix == 'ajax' || $prefix == 'json') && $isHttpRequest) {
            $this->_forward('jsonError');
        } else {

            $errors = $this->getRequest()->error_handler;
            if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER ||
                $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION ||
                $errors->exception instanceof Vps_Controller_Action_Web_Exception)
            {
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                $file = 'Error404.html';
            } else {
                $file = 'Error.html';
            }

            $paths = $this->view->getAllPaths();
            $scriptPath = $paths['script'][0];
            $path = '';
            if (!is_file($scriptPath . $file)) {
                $path = VPS_PATH . '/views/';
            }
            $this->view->setRenderFile($path . $file);
            $this->view->type = $errors->type;                    
            $this->view->exception = $errors->exception;                    
        }
        p($errors->exception);

    }

    public function jsonErrorAction()
    {
        $errors = $this->getRequest()->error_handler;
        $this->view->exceptions = $errors->exception->__toString();
    }
}
