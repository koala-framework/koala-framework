<?php
class Kwf_Component_Plugin_Inherit_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwf_Component_Plugin_Inherit_Root_PagesModel';
        $ret['generators']['page']['component'] = array();
        $ret['generators']['page']['component']['test1'] = 'Kwf_Component_Plugin_Inherit_Test1_Component';
        $ret['generators']['page']['component']['test2'] = 'Kwf_Component_Plugin_Inherit_Test2_Component';
        unset($ret['generators']['title']);
        return $ret;
    }
}
