<?php
class Kwc_User_Login_Facebook_ComponentController extends Kwf_Controller_Action
{
    public function jsonAuthAction()
    {
        $array = Kwf_Registry::get('config')->kwc->fbAppData;
        $adapter = new Kwc_User_Login_Facebook_Adapter($array);
        $token = $this->_getParam('code');

        if($token) {
            $adapter->setToken($token);
            $auth = Kwf_Auth::getInstance();
            $result  = $auth->authenticate($adapter);
            $auth->clearIdentity();
            return $adapter->authenticate();
        }
        return null;
    }
    protected function _isAllowedComponent()
    {
        return true;
    }
}
