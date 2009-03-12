<?php
class Vps_Crm_Customer_Model_Comments extends Vps_Model_Db_Proxy
{
    protected $_table = 'crm_customers_comments';
    protected $_rowClass = 'Vps_Crm_Customer_Model_Row_Comment';
    protected $_referenceMap = array(
        'InsertUser' => array(
            'column' => 'insert_uid',
            'refModelClass' => ''
        )
    );

    protected function _init()
    {
        $userModelClass = get_class(Vps_Registry::get('userModel'));
        $this->_referenceMap['InsertUser']['refModelClass'] = $userModelClass;
        parent::_init();
    }
}
