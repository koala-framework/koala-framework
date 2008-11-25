<?php
class Vps_Component_Generator_MultiBoxes_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['page1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'name' => 'page1'
        );

        $ret['generators']['box']['priority'] = 1;
        $ret['generators']['box']['component'] = array();
        $ret['generators']['box']['component']['foo'] = 'Vpc_MultiBox_Component';

        $ret['generators']['multibox1'] = array(
            'class' => 'Vps_Component_Generator_MultiBox_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'priority' => 2,
            'box' => 'foo',
            'inherit' => true
        );
        $ret['generators']['multibox2'] = array(
            'class' => 'Vps_Component_Generator_MultiBox_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'priority' => 1,
            'box' => 'foo',
            'inherit' => true
        );
        $ret['generators']['multibox3'] = array(
            'class' => 'Vps_Component_Generator_MultiBox_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'priority' => 3,
            'box' => 'foo',
            'inherit' => true
        );
        unset($ret['generators']['title']);
        return $ret;
    }
}
