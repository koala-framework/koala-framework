<?php
class Vps_Component_Generator_InheritComponentClass_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();

        $ret['generators']['page1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_InheritComponentClass_Page1',
            'name' => 'page1'
        );
        $ret['generators']['page2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_InheritComponentClass_Page2',
            'name' => 'page2'
        );

        $ret['generators']['box']['component']['box1'] = 'Vpc_Basic_Empty_Component';
        unset($ret['generators']['title']);
        return $ret;
    }

}
