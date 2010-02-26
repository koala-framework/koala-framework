<?php
class Vpc_Root_TrlRoot_TestComponent extends Vpc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['master']['component'] = 'Vpc_Root_TrlRoot_Master_TestComponent';
        /*
        $ret['generators']['chained']['component'] = array();
        $ret['generators']['chained']['component']['en'] = 'Vpc_Chained_Trl_Base_Component.Vpc_Root_TrlRoot_Master_TestComponent';
        $ret['generators']['chained']['component']['fr'] = 'Vpc_Chained_Trl_Base_Component.Vpc_Root_TrlRoot_Master_TestComponent';
        */
        $ret['generators']['chained']['component'] = 'Vpc_Root_TrlRoot_Slave_Component.Vpc_Root_TrlRoot_Master_TestComponent';
        $ret['generators']['chained']['name'] = 'en';
        return $ret;
    }
}
