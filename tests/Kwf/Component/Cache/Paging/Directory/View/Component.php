<?php
class Kwf_Component_Cache_Paging_Directory_View_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['paging'] = 'Kwf_Component_Cache_Paging_Directory_View_Paging_Component';
        return $ret;
    }
}
