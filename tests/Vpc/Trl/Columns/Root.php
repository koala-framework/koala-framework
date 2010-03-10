<?php
class Vpc_Trl_Columns_Root extends Vpc_Root_TrlRoot_Component
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
        $ret['generators']['master']['component'] = 'Vpc_Trl_Columns_German';
        $ret['generators']['chained']['component'] = 'Vpc_Root_TrlRoot_Chained_Component.Vpc_Trl_Columns_German';
        return $ret;
    }
}
