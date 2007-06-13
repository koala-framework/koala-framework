<?php
class Vps_Controller_Action_Error extends Vps_Controller_Action
{
    public function errorAction()
    {
        $prefix = substr($this->getRequest()->getParam('action'), 0, 4);
        $isHttpRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        if (($prefix == 'ajax' || $prefix == 'json') && $isHttpRequest) {
            $this->_helper->viewRenderer->setView(new Vps_View_Json()); // er läuft in ViewRenderer nicht nochmal in preDispatch() rein
            $this->_forward('jsonError');
        } else {
            $errors = $this->getRequest()->error_handler;
    /*
            switch ($errors->type) {
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                    // 404 error -- controller or action not found
                    $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
    
                    $content =<<<EOH
    <h1>Error!</h1>
    <p>The page you requested was not found.</p>
    <p>{$errors->exception->getMessage()}</p>
    EOH;
                    break;
                default:
                    // application error
                    $content =<<<EOH
    <h1>Error!</h1>
    <p>{$errors->exception->getMessage()}</p>
    EOH;
                    break;
            }
            $viewRenderer = $this->getHelper('ViewRenderer');
            $viewRenderer->setLayoutScript('error.html');
            $this->view->content = $content;
            */
            /*
            echo '<pre>';
            echo ($errors->exception);
            echo '</pre>';
            */
            throw($errors->exception);
        }

    }

    public function jsonErrorAction()
    {
        $errors = $this->getRequest()->error_handler;
        $this->view->exceptions = $errors->exception->__toString();
    }
}
