<?php
class Kwf_User_MessagesModel extends Kwf_Model_Db
{
    protected $_table = 'kwf_user_messages';
    protected $_rowClass = 'Kwf_User_MessageRow';

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
        $userModelClass = get_class(Kwf_Registry::get('userModel')->getEditModel());
        $this->_referenceMap['User']['refModelClass']  = $userModelClass;
        $this->_referenceMap['ByUser']['refModelClass']  = $userModelClass;
        parent::_init();
    }

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['message_date'] = new Kwf_Filter_Row_CurrentDateTime();
        $this->_filters['ip'] = new Kwf_Filter_Row_CurrentIp();
    }
}
