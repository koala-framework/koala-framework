<?php
class Vpc_Cc_Composite_Master_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['composite'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Cc_Composite_Master_Composite_Component',
        );
        $ret['flags']['chainedType'] = 'Cc';
        return $ret;
    }
}