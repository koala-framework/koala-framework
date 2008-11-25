<?php
class Vps_Component_Generator_IgnoreVisible_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['page']['component'] = array();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_IgnoreVisible_Child'
        );
        return $ret;
    }
}
?>