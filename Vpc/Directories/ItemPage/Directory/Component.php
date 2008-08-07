<?php
abstract class Vpc_Directories_ItemPage_Directory_Component extends Vpc_Directories_Item_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['class'] = 'Vps_Component_Generator_Page_Table';
        $ret['generators']['child']['component']['view'] = 'Vpc_Directories_List_ViewPage_Component';
        return $ret;
    }
}
