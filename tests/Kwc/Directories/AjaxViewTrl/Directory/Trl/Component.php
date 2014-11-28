<?php
class Kwc_Directories_AjaxViewTrl_Directory_Trl_Component extends Kwc_Directories_Item_Directory_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Kwc_Directories_AjaxViewTrl_Directory_Trl_Model';
        return $ret;
    }
}
