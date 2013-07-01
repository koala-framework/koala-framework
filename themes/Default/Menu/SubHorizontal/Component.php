<?php
class Default_Menu_SubHorizontal_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 2;
        $ret['cssClass'] .= ' webListNone';

        $ret['assets']['files'][] = 'kwf/themes/Default/Menu/SubHorizontal/Component.js';
        return $ret;
    }
}
