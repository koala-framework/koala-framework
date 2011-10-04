<?php
class Vps_Component_Generator_InheritNotFromPage_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);

        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'inherit' => true
        );

        $ret['generators']['page'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_InheritNotFromPage_Page',
            'name' => 'Static'
        );
        return $ret;
    }
}
