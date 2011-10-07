<?php
class Vpc_Box_InheritContent_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['page']['component'] = array();
        unset($ret['generators']['title']);
        $ret['generators']['box']['component'] = array(
            'ic' => 'Vpc_Box_InheritContent_InheritContent'
        );
        $ret['generators']['page1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Box_InheritContent_Page1',
            'name' => 'page1'
        );

        $ret['editComponents'] = array('ic');

        return $ret;
    }

}
