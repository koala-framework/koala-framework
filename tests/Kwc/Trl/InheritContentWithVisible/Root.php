<?php
class Kwc_Trl_InheritContentWithVisible_Root extends Kwc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        $ret['childModel'] = new Kwf_Model_FnF(array(
            'toStringField' => 'name',
            'data' => array(
                array('id'=>'de', 'filename'=>'de', 'name'=>'de', 'master'=>true),
                array('id'=>'en', 'filename'=>'en', 'name'=>'en', 'master'=>false),
            )
        ));
        $ret['generators']['de'] = array(
            'class' => 'Kwc_Chained_Trl_MasterGenerator',
            'component' => 'Kwc_Trl_InheritContentWithVisible_German',
            'name' => 'de'
        );
        $ret['generators']['en'] = array(
            'class' => 'Kwc_Chained_Trl_ChainedGenerator',
            'component' => 'Kwc_Trl_InheritContentWithVisible_English.Kwc_Trl_InheritContentWithVisible_German',
            'name' => 'en'
        );
        return $ret;
    }
}
