<?php
class Vpc_Trl_StaticTextsForm_GermanMaster extends Vpc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['category']);
        $ret['generators']['testtrl'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_StaticTextsForm_Translate_Component',
            'name' => 'testtrl',
        );
        return $ret;
    }
}
