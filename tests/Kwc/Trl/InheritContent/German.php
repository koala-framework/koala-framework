<?php
class Kwc_Trl_InheritContent_German extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators'] = array();
        $ret['editComponents'] = array();

        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_InheritContent_Test_Component',
            'name' => 'test',
        );
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Trl_InheritContent_Box_Component',
            'inherit' => true
        );
        return $ret;
    }
}
