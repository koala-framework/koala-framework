<?php
class Vps_Controller_Action_Error_CliController extends Vps_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->getRequest()->getParam('error_handler');
        if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER ||
            $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION ||
            $errors->exception instanceof Vps_Controller_Action_Web_FileNotFoundException) {
            echo "ERROR: Controller not found\n\n";
            $this->_forward('index', 'help', 'vps_controller_action_cli');
        } else {
            echo $errors->exception->__toString();
            exit;
        }
    }
}
