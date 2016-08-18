<?php
class Kwf_Component_Cache_Directory_Root_Directory_Component extends Kwc_Directories_Item_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwf_Component_Cache_Directory_Root_Directory_Model';
        //$ret['generators']['detail']['component'] = 'Kwf_Component_Cache_Directory_Root_Directory_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'Kwf_Component_Cache_Directory_Root_Directory_View_Component';
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
