<?php
class Kwf_User_AuthPassword_FnF extends Kwf_Model_FnF
{
    protected $_toStringField = 'email';
    protected function _init()
    {
        $this->_data = array(
            array('id'=>1, 'email' => 'test@vivid.com', 'password' => md5('foo'.'123'), 'password_salt' => '123', 'deleted'=>false, 'locked'=>false),
            array('id'=>2, 'email' => 'testdel@vivid.com', 'password' => md5('bar'.'1234'), 'password_salt' => '1234', 'deleted'=>true, 'locked'=>false),
            array('id'=>3, 'email' => 'testlock@vivid.com', 'password' => md5('xxx'.'1235'), 'password_salt' => '1235', 'deleted'=>false, 'locked'=>true),
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
