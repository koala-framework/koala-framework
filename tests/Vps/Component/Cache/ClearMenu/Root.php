<?php
class Vps_Component_Cache_ClearMenu_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['page']['model'] = new Vps_Component_Cache_ClearMenu_RootModel();
        $ret['generators']['page']['component']['link'] = 'Vps_Component_Cache_ClearMenu_Link';
        $ret['generators']['menu'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Menu_Component',
            'box' => 'menu'
        );
        $ret['generators']['box']['component'] = array();
        unset($ret['generators']['title']);
        return $ret;
    }
}
