<?php
class Vpc_Trl_StaticTextsPlaceholder_Root extends Vpc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);

        $ret['generators']['master']['component'] =
            'Vpc_Trl_StaticTextsPlaceholder_GermanMaster';
        $ret['generators']['chained']['component'] =
            'Vpc_Root_TrlRoot_Chained_Component.Vpc_Trl_StaticTextsPlaceholder_GermanMaster';

        $ret['childModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>'1', 'filename'=>'de', 'name'=>'Deutsch', 'master'=>true),
                array('id'=>'2', 'filename'=>'en', 'name'=>'English', 'master'=>false),
            )
        ));

        return $ret;
    }
}
