<?php
class Vpc_Forum_Posts_Detail_Actions_Delete_Component extends Vpc_Posts_Detail_Delete_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['confirmed']['component'] = 'Vpc_Forum_Posts_Detail_Actions_Delete_Confirmed_Component';
        return $ret;
    }
}
