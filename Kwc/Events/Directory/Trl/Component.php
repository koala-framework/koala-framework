<?php
class Kwc_Events_Directory_Trl_Component extends Kwc_Directories_Item_Directory_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Kwc_Events_Directory_Trl_Model';
        $ret['menuConfig'] = 'Kwf_Component_Abstract_MenuConfig_Trl_SameClass';
        return $ret;
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $ret->order('start_date', 'ASC');
        return $ret;
    }
}
