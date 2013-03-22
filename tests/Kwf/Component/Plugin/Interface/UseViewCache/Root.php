<?php
class Kwf_Component_Plugin_Interface_UseViewCache_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwf_Component_Plugin_Interface_UseViewCache_Root_PagesModel';
        $ret['generators']['page']['component'] = array();
        $ret['generators']['page']['component']['withUseViewCache'] = 'Kwf_Component_Plugin_Interface_UseViewCache_Component';
        unset($ret['generators']['title']);
        return $ret;
    }
}
