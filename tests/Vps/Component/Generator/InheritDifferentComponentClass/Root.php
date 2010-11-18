<?php
class Vps_Component_Generator_InheritDifferentComponentClass_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);

        $ret['generators']['page1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_InheritDifferentComponentClass_Page1',
            'name' => 'page1'
        );

        $ret['generators']['box']['component']['box'] = 'Vps_Component_Generator_InheritDifferentComponentClass_Box_Component';
        unset($ret['generators']['title']);
        return $ret;
    }

}
