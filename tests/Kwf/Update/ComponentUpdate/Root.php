<?php
class Kwf_Update_ComponentUpdate_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array('test' => 'Kwf_Update_ComponentUpdate_TestComponent_Component');

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }

}
