<?php
class Kwf_Controller_Action_User_LogoutController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        Kwf_Auth::getInstance()->clearIdentity();
        setcookie('feAutologin', '', time() - 3600, '/', null, Kwf_Util_Https::supportsHttps(), true);
        setcookie('hasFeAutologin', '', time() - 3600, '/', null, false, true);
        $url = $this->_getParam('redirect') ? $this->_getParam('redirect') : '/';
        header('Location: ' . $url);
        exit;
    }
}
