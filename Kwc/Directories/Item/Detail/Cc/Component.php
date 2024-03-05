<?php
class Kwc_Directories_Item_Detail_Cc_Component extends Kwc_Abstract_Composite_Cc_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['hasModifyItemData'] = true;
        return $ret;
    }

    public static function modifyItemData(Kwf_Component_Data $item)
    {
        call_user_func(array($item->chained->componentClass, 'modifyItemData'), $item);
    }
}
