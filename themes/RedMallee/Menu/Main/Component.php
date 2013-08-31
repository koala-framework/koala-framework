<?php
class RedMallee_Menu_Main_Component extends Kwc_Menu_Dropdown_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'main';
        $ret['cssClass'] .= ' webListNone';
        $ret['assets']['files'][] = 'kwf/themes/RedMallee/Menu/Main/Component.js';
        $ret['assets']['dep'][] = 'KwfResponsiveEl';
        $ret['generators']['subMenu'] = array(
            'class' => 'Kwc_Menu_Generator',
            'component' => 'RedMallee_Menu_SubDropdown_Component'
        );
        return $ret;
    }
    protected function _getMenuData($parentData = null, $select = array())
    {
        $ret = parent::_getMenuData($parentData, $select);
        foreach ($ret as $k=>$i) {
            if (count($ret[$k]['data']->getChildPages(array('showInMenu'=>true))) > 0) {
                $ret[$k]['class'] .= ' hasSubmenu';
            }
        }
        return $ret;
    }
}