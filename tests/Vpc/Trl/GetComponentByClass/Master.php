<?php
class Vpc_Trl_GetComponentByClass_Master extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'name' => 'test1',
        );
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
