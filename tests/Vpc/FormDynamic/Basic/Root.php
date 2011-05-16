<?php
class Vpc_FormDynamic_Basic_Root extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['form'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'form',
            'component' => 'Vpc_FormDynamic_Basic_Form_Component'
        );
        $ret['generators']['form2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'form2',
            'component' => 'Vpc_FormDynamic_Basic_Form_Component'
        );
        return $ret;
    }
}