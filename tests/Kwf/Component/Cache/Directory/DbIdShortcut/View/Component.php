<?php
class Kwf_Component_Cache_Directory_DbIdShortcut_View_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['partialClass'] = 'Kwf_Component_Partial_Id';
        return $ret;
    }
}
