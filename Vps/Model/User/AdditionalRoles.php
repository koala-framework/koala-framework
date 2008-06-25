<?php
class Vps_Model_User_AdditionalRoles extends Vps_Db_Table
{
    protected $_name = 'vps_users_additional_roles';
    protected $_primary = 'id';

    protected $_referenceMap = array(
        'Users' => array(
            'columns'           => 'user_id',
            'refTableClass'     => '',
            'refColumns'        => 'id'
        )
    );

    protected function _setup()
    {
        $this->_referenceMap['Users']['refTableClass'] = get_class(Vps_Registry::get('userModel'));
        parent::_setup();
    }
}
