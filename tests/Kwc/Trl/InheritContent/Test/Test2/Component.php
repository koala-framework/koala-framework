<?php
class Kwc_Trl_InheritContent_Test_Test2_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['test3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_InheritContent_Test_Test2_Test3_Component',
            'name' => 'test3'
        );
        return $ret;
    }
}
