<?php
class Vps_Controller_Action_Error_CliController extends Vps_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->getRequest()->getParam('error_handler');
        if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER ||
            $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION)
        {
            if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER) {
                file_put_contents('php://stderr', "ERROR: Controller not found\n");
            } else if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION) {
                file_put_contents('php://stderr', "ERROR: Action not found\n");
            }
        } else if ($errors->exception instanceof Vps_ClientException) {
            file_put_contents('php://stderr', $errors->exception->getMessage()."\n");
        } else {
            file_put_contents('php://stderr', $errors->exception->__toString()."\n");
        }
        exit(1);
    }
}
