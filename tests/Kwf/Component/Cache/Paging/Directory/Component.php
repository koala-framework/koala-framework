<?php
class Kwf_Component_Cache_Paging_Directory_Component extends Kwc_Directories_Item_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['view'] = 'Kwf_Component_Cache_Paging_Directory_View_Component';
        $ret['childModel'] = 'Kwf_Component_Cache_Paging_Directory_Model';
        return $ret;
    }
}
