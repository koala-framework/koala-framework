<?php
class Kwf_User_AuthPassword_FnF extends Kwf_Model_FnF
{
    protected $_toStringField = 'email';
    protected $_hasDeletedFlag = true;

    protected function _init()
    {
        $this->_data = array(
            array('id'=>1, 'email' => 'test@vivid.com', 'password' => md5('foo'.'123'), 'password_salt' => '123', 'deleted'=>false),
            array('id'=>2, 'email' => 'testdel@vivid.com', 'password' => md5('bar'.'1234'), 'password_salt' => '1234', 'deleted'=>true),
        );
        parent::_init();
    }

    public function getAuthMethods()
    {
        return array(
            'password' => new Kwf_User_Auth_PasswordFields($this),
            'activation' => new Kwf_User_Auth_ActivationFields($this),
        );
    }
}
