<?php
class Vpc_Forum_Group_ModeratorsModel extends Vps_Db_Table_Abstract
{
    protected $_name = 'vpc_forum_moderators';
    protected $_referenceMap = array(
        'Group' => array(
            'columns'           => array('group_id'),
            'refTableClass'     => 'Vpc_Forum_Directory_Model',
            'refColumns'        => array('id')
        ),
        'User' => array(
            'columns'           => array('user_id'),
            'refTableClass'     => 'Vps_Model_User_Users',
            'refColumns'        => array('id')
        )
    );
}