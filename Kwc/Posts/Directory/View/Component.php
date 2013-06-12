<?php
class Kwc_Posts_Directory_View_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['partialClass'] = 'Kwf_Component_Partial_Id';
        $ret['placeholder']['noEntriesFound'] = false;
        return $ret;
    }
}
