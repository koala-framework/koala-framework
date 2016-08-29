<?php
class Kwc_Directories_AjaxViewMultipleDirectories_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Directories_AjaxViewMultipleDirectories_Directory_Model';
        $ret['generators']['detail']['component'] = 'Kwc_Directories_AjaxViewMultipleDirectories_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_AjaxViewMultipleDirectories_View_Component';
        return $ret;
    }
}
