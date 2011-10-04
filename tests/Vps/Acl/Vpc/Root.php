<?php
class Vps_Acl_Vpc_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['page']['component'] = array(
            'test' => 'Vps_Acl_Vpc_TestComponent'
        );
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Empty_Component'
        );
        $ret['generators']['box']['component'] = array();
        unset($ret['generators']['title']);
        return $ret;
    }
}
