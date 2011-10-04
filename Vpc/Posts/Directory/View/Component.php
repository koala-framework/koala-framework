<?php
class Vpc_Posts_Directory_View_Component extends Vpc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['partialClass'] = 'Vps_Component_Partial_Id';
        return $ret;
    }
}
