<?php
class Vps_Update_ComponentUpdate_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['page']['component'] = array('test' => 'Vps_Update_ComponentUpdate_TestComponent_Component');

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }

}
