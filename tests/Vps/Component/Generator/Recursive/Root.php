<?php
class Vps_Component_Generator_Recursive_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['page']['component'] = array('empty' => 'Vpc_Basic_Empty_Component');
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_Recursive_Static',
            'name' => 'Static'
        );
        return $ret;
    }
}
?>