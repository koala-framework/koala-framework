<?php
class Vpc_Root_TrlRoot_Slave_Component extends Vpc_Chained_Trl_Base_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['class'] = 'Vpc_Root_Category_Trl_Generator';
        $ret['generators']['page']['model'] = 'Vpc_Root_TrlRoot_Slave_Model';
        return $ret;
    }
}
