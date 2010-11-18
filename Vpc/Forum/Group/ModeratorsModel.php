<?php
class Vpc_Forum_Group_ModeratorsModel extends Vps_Model_Db
{
    protected $_table = 'vpc_forum_moderators';
    protected $_referenceMap = array(
        'Group' => array(
            'column' => 'group_id',
            'refModelClass' => 'Vpc_Forum_Directory_Model'
        ),
        'User' => array(
            'column' => 'user_id',
            'refModelClass' => null
        )
    );

    protected function _init()
    {
        $userModelClass = get_class(Vps_Registry::get('userModel'));
        $this->_referenceMap['User']['refModelClass'] = $userModelClass;

        parent::_init();
    }
}