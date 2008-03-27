<?php
class Vpc_Forum_ModeratorModel extends Vps_Db_Table_Abstract
{
    protected $_name = 'vpc_forum_moderators';
    protected $_rowClass = 'Vpc_Forum_ModeratorRow';

    protected $_referenceMap = array(
        'User' => array('columns' => 'user_id',
                                    'refTableClass' => 'Vpc_Forum_User_Model',
                                    'refColumns' => 'id'),
        'Group'  => array('columns' => 'group_id',
                                    'refTableClass' => 'Vpc_Forum_Group_Model',
                                    'refColumns' => 'id'));

    protected function _setup()
    {
        $this->_referenceMap['ServiceUser'] = array(
            'columns' => 'user_id',
            'refTableClass' => get_class(Zend_Registry::get('userModel')),
            'refColumns' => 'id'
        );
        parent::_setup();
    }
}