<?php
abstract class Kwc_Directories_ItemPage_Directory_Component extends Kwc_Directories_Item_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['class'] = 'Kwf_Component_Generator_Page_Table';
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_List_ViewPage_Component';
        return $ret;
    }
}
