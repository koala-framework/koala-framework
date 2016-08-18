<?php
class Kwf_Component_Cache_Directory_Root_Directory_View_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['partialClass'] = 'Kwf_Component_Partial_Id';
        $ret['placeholder']['noEntriesFound'] = '';
        return $ret;
    }
}
