<?php
class Kwc_Menu_ParentContent_Component extends Kwc_Basic_ParentContent_Component
{
    public static function getSettings($menuComponentClass)
    {
        $ret = parent::getSettings();
        $ret['menuComponentClass'] = $menuComponentClass;
        $ret['viewCache'] = Kwc_Abstract::getSetting($menuComponentClass, 'viewCache');
        return $ret;
    }
}
