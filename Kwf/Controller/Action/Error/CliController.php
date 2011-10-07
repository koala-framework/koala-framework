<?php
class Kwf_Controller_Action_Error_CliController extends Kwf_Controller_Action
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
        } else if ($errors->exception instanceof Kwf_Exception_Client) {
            file_put_contents('php://stderr', $errors->exception->getMessage()."\n");
        } else {
            throw $errors->exception; // wird von Kwf_Debug::handleException behandelt
        }
        exit(1);
    }
}
