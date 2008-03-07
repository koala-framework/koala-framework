<?php
class Vpc_Forum_ModeratorModel extends Vps_Db_Table_Abstract
{
    protected $_name = 'vpc_forum_moderators';

    protected $_referenceMap = array(
        'User' => array('columns' => 'user_id',
                                    'refTableClass' => 'Vpc_Forum_User_Model',
                                    'refColumns' => 'id'),
        'Group'  => array('columns' => 'group_id',
                                    'refTableClass' => 'Vpc_Forum_Group_Model',
                                    'refColumns' => 'id'));
}