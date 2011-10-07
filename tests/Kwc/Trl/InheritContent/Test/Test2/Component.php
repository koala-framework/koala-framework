<?php
class Vpc_Trl_InheritContent_Test_Test2_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test3'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_InheritContent_Test_Test2_Test3_Component',
            'name' => 'test3'
        );
        return $ret;
    }
}
