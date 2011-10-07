<?php
class Vps_Component_Generator_InheritDifferentComponentClass_Page1 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'name' => 'page2'
        );
        return $ret;
    }

}
