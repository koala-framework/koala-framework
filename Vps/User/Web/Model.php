<?php
class Vps_User_Web_Model extends Vps_Model_Db
{
    protected $_table = 'vps_users';
    protected $_referenceMap = array(
        'User' => array(
            'column' => 'id',
            'refModelClass' => ''
        )
    );
    protected $_default = array('role' => 'guest');

    protected function _init()
    {
        $userModelClass = get_class(Vps_Registry::get('userModel'));
        $this->_referenceMap['User']['refModelClass']  = $userModelClass;
        parent::_init();
    }
}
