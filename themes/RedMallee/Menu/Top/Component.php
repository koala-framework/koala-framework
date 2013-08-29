<?php
class RedMallee_Menu_Top_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'top';
        $ret['cssClass'] .= ' webListNone webStandard';
        $ret['assets']['files'][] = 'kwf/themes/RedMallee/Menu/Top/Component.js';
        $ret['assets']['dep'][] = 'KwfResponsiveEl';
        return $ret;
    }
}
