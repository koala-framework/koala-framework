<?php
class Kwc_Menu_DropdownMask_Component extends Kwc_Menu_Dropdown_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] .= ' webListNone';
        $ret['assets']['dep'][] = 'jQuery';
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