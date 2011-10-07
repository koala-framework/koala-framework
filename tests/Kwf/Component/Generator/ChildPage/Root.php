<?php
class Vps_Component_Generator_ChildPage_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['page']['component'] = array();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_ChildPage_Child'
        );
        $ret['generators']['box']['component'] = array('form' => 'Vpc_Form_Component');
        unset($ret['generators']['title']);
        return $ret;
    }
}
?>