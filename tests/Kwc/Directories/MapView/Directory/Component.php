<?php
class Kwc_Directories_MapView_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Directories_MapView_Directory_Model';
        $ret['generators']['detail']['component'] = 'Kwc_Directories_MapView_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_MapView_View_Component';
        return $ret;
    }
}
