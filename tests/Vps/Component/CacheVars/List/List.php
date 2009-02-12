<?php
class Vps_Component_CacheVars_List_List extends Vpc_Directories_Item_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['model'] = 'Vps_Component_CacheVars_List_Model';
        return $ret;
    }
}
