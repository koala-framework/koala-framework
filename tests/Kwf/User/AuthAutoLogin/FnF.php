<?php
class Kwf_User_AuthAutoLogin_FnF extends Kwf_Model_FnF
{
    protected $_toStringField = 'email';
    protected $_hasDeletedFlag = true;

    protected function _init()
    {
        $this->_data = array(
            array('id'=>1, 'email' => 'test@vivid.com', 'autologin' => null, 'password_salt' => '123', 'deleted'=>false),
            array('id'=>2, 'email' => 'testdel@vivid.com', 'autologin' => null, 'password_salt' => '1234', 'deleted'=>true),
        );
        parent::_init();
    }

    public function getAuthMethods()
    {
        return array(
            'autoLogin' => new Kwf_User_Auth_AutoLoginFields($this)
        );
    }
}
