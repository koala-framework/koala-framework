<?php
class Kwf_User_AuthPasswordProxy_FnF extends Kwf_Model_FnF
{
    protected function _init()
    {
        $this->_data = array(
            array('id'=>1, 'email' => 'test@vivid.com', 'password' => md5('foo'.'123'), 'password_salt' => '123', 'deleted'=>false),
        );
        parent::_init();
    }

    public function getAuthMethods()
    {
        return array(
            'password' => new Kwf_User_Auth_PasswordFields($this)
        );
    }
}
