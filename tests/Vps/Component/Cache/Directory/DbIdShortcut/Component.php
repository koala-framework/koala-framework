<?php
class Vps_Component_Cache_Directory_DbIdShortcut_Component extends Vpc_Directories_Item_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['dbIdShortcut'] = 'foo_';
        $ret['childModel'] = 'Vps_Component_Cache_Directory_DbIdShortcut_Model';
        $ret['generators']['child']['component']['view'] = 'Vps_Component_Cache_Directory_DbIdShortcut_View_Component';
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
