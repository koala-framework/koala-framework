<?php
class Vpc_Trl_FormDynamic_Master extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_FormDynamic_Form_Component',
            'name' => 'test1',
        );
        return $ret;
    }
}
