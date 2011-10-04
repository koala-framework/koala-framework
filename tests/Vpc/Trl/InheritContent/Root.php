<?php
class Vpc_Trl_InheritContent_Root extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        $ret['childModel'] = new Vps_Model_FnF(array(
            'toStringField' => 'name',
            'data' => array(
                array('id'=>'de', 'filename'=>'de', 'name'=>'de', 'master'=>true),
                array('id'=>'en', 'filename'=>'en', 'name'=>'en', 'master'=>false),
            )
        ));
        $ret['generators']['de'] = array(
            'class' => 'Vpc_Chained_Trl_MasterGenerator',
            'component' => 'Vpc_Trl_InheritContent_German',
            'name' => 'de'
        );
        $ret['generators']['en'] = array(
            'class' => 'Vpc_Chained_Trl_ChainedGenerator',
            'component' => 'Vpc_Trl_InheritContent_English.Vpc_Trl_InheritContent_German',
            'name' => 'en'
        );
        return $ret;
    }
}
