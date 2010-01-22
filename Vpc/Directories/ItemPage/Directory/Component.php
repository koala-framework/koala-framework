<?php
abstract class Vpc_Directories_ItemPage_Directory_Component extends Vpc_Directories_Item_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['class'] = 'Vpc_CnImmobilien_Immovables_Detail_ChildImmovables_Directory_Generator';
        $ret['generators']['child']['component']['view'] = 'Vpc_Directories_List_ViewPage_Component';
        return $ret;
    }
}
