<?php
class Vps_Component_Cache_UrlClearCache_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vps_Component_Cache_UrlClearCache_PageTestModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component'
        );
        return $ret;
    }
}