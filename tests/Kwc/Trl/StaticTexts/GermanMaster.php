<?php
class Kwc_Trl_StaticTexts_GermanMaster extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['category']);
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_StaticTexts_Test_Component',
            'name' => 'test',
        );
        return $ret;
    }
}
