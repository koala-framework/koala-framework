<?php
class Kwc_Directories_Item_Detail_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['hasModifyItemData'] = true;
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['dataClass'] = 'Kwc_Directories_Item_Detail_Data';
        return $ret;
    }
    public static function modifyItemData(Kwf_Component_Data $item)
    {
    }
}
