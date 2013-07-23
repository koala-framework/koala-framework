<?php
class Kwc_User_Login_Facebook_ComponentController extends Kwf_Controller_Action
{
    public function jsonAuthAction()
    {
        $array = Kwf_Registry::get('config')->kwc->fbAppData;
        $adapter = new Kwc_User_Login_Facebook_Adapter($array);
        $token = $this->_getParam('accessToken');

        if($token) {
            $adapter->setToken($token);
            return $adapter->authenticate();
        }
        return null;
    }
    protected function _isAllowedComponent()
    {
        return true;
    }
}
