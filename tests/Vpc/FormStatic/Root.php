<?php
class Vpc_FormStatic_Root extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['form'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'form',
            'component' => 'Vpc_FormStatic_Form_Component'
        );
        return $ret;
    }
}