<?php
class Vps_Controller_Action_Debug_ActivateController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $session = new Zend_Session_Namespace('debug');
        $session->enable = true;
        if ($this->_getParam('url')) {
            header('Location: '.$this->_getParam('url'));
        }
        exit;
    }
    public function jsonDeactivateAction()
    {
        $session = new Zend_Session_Namespace('debug');
        $session->enable = false;
    }
}
