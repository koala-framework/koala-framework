<?php
class Vpc_Root_TrlRoot_TestComponent extends Vpc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['master']['component'] = 'Vpc_Root_TrlRoot_Master_TestComponent';
        $ret['generators']['chained']['component'] = 'Vpc_Root_TrlRoot_Slave_Component.Vpc_Root_TrlRoot_Master_TestComponent';
        $ret['generators']['chained']['model'] = new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>'1', 'filename'=>'en', 'name'=>'en'),
            )
        ));
        return $ret;
    }
}
