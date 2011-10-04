<?php
class Vpc_Events_Directory_Trl_Component extends Vpc_Directories_Item_Directory_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Vpc_Events_Directory_Trl_Model';
        $ret['flags']['hasResources'] = true;
        return $ret;
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $ret->order('start_date', 'ASC');
        return $ret;
    }
}
