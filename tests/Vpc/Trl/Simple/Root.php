<?php
class Vpc_Trl_Simple_Root extends Vpc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['childModel'] = new Vps_Model_FnF(array(
            'toStringField' => 'name',
            'data' => array(
                array('id'=>'1', 'filename'=>'de', 'name'=>'de', 'master'=>true),
                array('id'=>'2', 'filename'=>'en', 'name'=>'en', 'master'=>false),
            )
        ));
        $ret['generators']['master']['component'] = 'Vpc_Trl_Simple_German';
        $ret['generators']['chained']['component'] = 'Vpc_Trl_Simple_English.Vpc_Trl_Simple_German';
        return $ret;
    }
}
