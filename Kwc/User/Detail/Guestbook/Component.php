<?php
class Kwc_User_Detail_Guestbook_Component extends Kwc_Posts_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Guestbook');
        $ret['placeholder']['writeText'] = trlKwfStatic('New Entry');
        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $select->order('create_time', 'DESC');
        return $select;
    }
}
