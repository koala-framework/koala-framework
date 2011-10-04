<?php
class Vps_Component_Cache_Directory_DbIdShortcut_Trl_Component extends Vpc_Directories_Item_Directory_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Vps_Component_Cache_Directory_DbIdShortcut_Trl_Model';
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
