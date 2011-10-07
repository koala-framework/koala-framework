<?php
class Kwc_Directories_Top_Directory extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Directories_Top_Model';
        $ret['generators']['detail']['nameColumn'] = 'name';
        return $ret;
    }
}
