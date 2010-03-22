<?php
class Vpc_Directories_Item_Detail_Trl_Component extends Vpc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['hasModifyItemData'] = true;
        return $ret;
    }
    public static function modifyItemData(Vps_Component_Data $item)
    {
    }
}
