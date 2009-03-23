<?php
class Vps_User_AdditionalRoles extends Vps_Model_Db
{
    protected $_table = 'vps_users_additional_roles';

    protected $_referenceMap = array(
        'Users' => array(
            'column'           => 'user_id',
            'refModelClass'     => ''
        )
    );

    protected function _init()
    {
        $this->_referenceMap['Users']['refModelClass'] = get_class(Vps_Registry::get('userModel'));
        parent::_init();
    }
}
