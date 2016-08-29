<?php
class Kwc_Trl_MenuCache_MainMenu_Component extends Kwc_Menu_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['level'] = 'main';
        $ret['rootElementClass'] .= ' webListNone';

        $ret['generators']['subMenu'] = array(
            'class' => 'Kwc_Menu_Generator',
            'component' => 'Kwc_Trl_MenuCache_MainMenu_SubMenu_Component'
        );

        return $ret;
    }
}
