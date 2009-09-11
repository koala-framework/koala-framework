<?php
class Vpc_Guestbook_Component extends Vpc_Posts_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'Vpc_Guestbook_Detail_Component';
        $ret['generators']['write']['component'] = 'Vpc_Guestbook_Write_Component';
        return $ret;
    }

    public function getSelect($overrideValues = array())
    {
        $ret = parent::getSelect($overrideValues);
        $ret->order('id', 'DESC');
        return $ret;
    }
}
