<?php
class Vpc_User_Detail_Guestbook_Component extends Vpc_Posts_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Guestbook');
        return $ret;
    }
    public function getSelect()
    {
        $select = parent::getSelect();
        $select->order('create_time', 'DESC');
        return $select;
    }
}
