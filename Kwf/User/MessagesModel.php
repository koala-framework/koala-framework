<?php
class Vps_User_MessagesModel extends Vps_Model_Db
{
    protected $_table = 'vps_user_messages';
    protected $_rowClass = 'Vps_User_MessageRow';

    protected $_referenceMap = array(
        'User' => array(
            'column' => 'user_id',
            'refModelClass' => ''
        ),
        'ByUser' => array(
            'column' => 'by_user_id',
            'refModelClass' => ''
        )
    );

    protected function _init()
    {
        $userModelClass = get_class(Vps_Registry::get('userModel'));
        $this->_referenceMap['User']['refModelClass']  = $userModelClass;
        $this->_referenceMap['ByUser']['refModelClass']  = $userModelClass;
        parent::_init();
    }

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['message_date'] = new Vps_Filter_Row_CurrentDateTime();
        $this->_filters['ip'] = new Vps_Filter_Row_CurrentIp();
    }
}
