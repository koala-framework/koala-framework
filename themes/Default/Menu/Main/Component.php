<?php
class Default_Menu_Main_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'main';
        $ret['cssClass'] .= ' webListNone';
        $ret['assets']['files'][] = 'kwf/themes/Default/Menu/Main/Component.js';
        $ret['assets']['dep'][] = 'KwfResponsiveEl';
        return $ret;
    }
}
