<?php
class Vps_Component_Generator_InheritNotFromPage_PageChild extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vps_Component_Generator_Inherit_Box',
            'inherit' => true
        );
        $ret['generators']['page2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_InheritNotFromPage_Page2',
            'name' => 'page2'
        );
        return $ret;
    }
}
