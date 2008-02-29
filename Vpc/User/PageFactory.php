<?php
class Vpc_User_PageFactory extends Vpc_Abstract_StaticPageFactory
{
    protected $_pages = array();

    protected function _init()
    {
        parent::_init();
        $this->_pages[] = array(
            'componentClass' => $this->_getChildComponentClass('activate'),
            'showInMenu' => false,
            'name' => 'User Activation',
            'id' => 'useractivate'
        );
        $this->_pages[] = array(
            'componentClass' => $this->_getChildComponentClass('edit'),
            'showInMenu' => false,
            'name' => 'Edit User',
            'id' => 'useredit'
        );
        $this->_pages[] = array(
            'componentClass' => $this->_getChildComponentClass('login'),
            'showInMenu' => false,
            'name' => 'User Login',
            'id' => 'userlogin'
        );
        $this->_pages[] = array(
            'componentClass' => $this->_getChildComponentClass('register'),
            'showInMenu' => false,
            'name' => 'User Registration',
            'id' => 'userreg'
        );
    }
}