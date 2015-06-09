<?php
class Kwf_Controller_Action_User_LogoutController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        Kwf_Auth::getInstance()->clearIdentity();
        Kwf_User_Autologin::clearCookies();
        Kwf_Session::destroy();
        Kwf_Util_Redirect::redirect($this->_getParam('redirect'));
    }

    protected function _isAllowedResource()
    {
        return true;
    }
}
