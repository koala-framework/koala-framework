<?php
class Kwf_Controller_Action_User_LogoutController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        Kwf_Auth::getInstance()->clearIdentity();
        $url = $this->_getParam('redirect') ? $this->_getParam('redirect') : '/';
        header('Location: ' . $url);
        exit;
    }
}
