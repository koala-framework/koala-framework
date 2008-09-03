<?php
class Vps_Component_Generator_Priority_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['page1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_Priority_Page1',
            'name' => 'page1'
        );

        $ret['generators']['box']['priority'] = 1;
        $ret['generators']['box']['component'] = array();
        $ret['generators']['box']['component']['foo'] = 'Vpc_Basic_Empty_Component';
        return $ret;
    }
}
