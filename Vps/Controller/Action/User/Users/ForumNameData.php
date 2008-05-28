<?php
class Vps_Controller_Action_User_Users_ForumNameData extends Vps_Auto_Data_Abstract
{

    public function load($row)
    {
        $table = new Vpc_Forum_User_Model();
        $forumUser = $table->fetchRow(array('id = ?' => $row->id));
        if (!$forumUser || empty($forumUser->nickname)) {
            return '';
        } else {
            return $forumUser->nickname;
        }
    }

    public function save(Zend_Db_Table_Row_Abstract $row, $data)
    {
        $table = new Vpc_Forum_User_Model();
        $forumUser = $table->fetchRow(array('id = ?' => $row->id));
        if ($forumUser) {
            $forumUser->nickname = $data;
            $forumUser->save();
        }
    }

}
