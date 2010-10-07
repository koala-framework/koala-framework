<?php
class Vps_Component_Cache_Directory_Root_Directory_Component extends Vpc_Directories_Item_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vps_Component_Cache_Directory_Root_Directory_Model';
        //$ret['generators']['detail']['component'] = 'Vps_Component_Cache_Directory_Root_Directory_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'Vps_Component_Cache_Directory_Root_Directory_View_Component';
        return $ret;
    }
}
