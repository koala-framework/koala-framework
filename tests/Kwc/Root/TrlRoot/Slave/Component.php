<?php
class Kwc_Root_TrlRoot_Slave_Component extends Kwc_Root_TrlRoot_Chained_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['class'] = 'Kwc_Root_Category_Trl_Generator';
        $ret['generators']['page']['model'] = 'Kwc_Root_TrlRoot_Slave_Model';
        return $ret;
    }
}
