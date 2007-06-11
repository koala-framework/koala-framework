<?php
class Vps_Controller_Action_Error extends Vps_Controller_Action
{
    public function errorAction()
    {
        $request = $this->getRequest();
        $errors = $request->error_handler;

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
    }
}
