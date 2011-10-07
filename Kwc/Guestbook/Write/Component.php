<?php
class Vpc_Guestbook_Write_Component extends Vpc_Posts_Write_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Guestbook_Write_Form_Component';
        $ret['plugins'] = array();
        return $ret;
    }
}
