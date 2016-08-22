<?php
class Kwc_Directories_AjaxViewMultipleDirectories_List_Component extends Kwc_Directories_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['useDirectorySelect'] = false;
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_AjaxViewMultipleDirectories_View_Component';
        return $ret;
    }

    public static function getItemDirectoryClasses($componentClass)
    {
        return array('Kwc_Directories_AjaxViewMultipleDirectories_Directory_Component');
    }

    protected function _getItemDirectory()
    {
        return 'Kwc_Directories_AjaxViewMultipleDirectories_Directory_Component';
    }
}
