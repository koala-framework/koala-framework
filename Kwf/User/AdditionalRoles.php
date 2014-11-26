<?php
class Kwf_User_AdditionalRoles extends Kwf_Model_Db
{
    protected $_table = 'kwf_users_additional_roles';

    protected $_referenceMap = array(
        'Users' => array(
            'column'           => 'user_id',
            'refModelClass'     => ''
        )
    );

    protected function _init()
    {
        $this->_referenceMap['Users']['refModelClass'] = get_class(Kwf_Registry::get('userModel'));
        parent::_init();
    }
}
