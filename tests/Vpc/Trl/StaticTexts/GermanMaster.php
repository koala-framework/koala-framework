<?php
class Vpc_Trl_StaticTexts_GermanMaster extends Vpc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['category']);
        $ret['generators']['test'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_StaticTexts_Test_Component',
            'name' => 'test',
        );
        return $ret;
    }
}
