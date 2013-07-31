<?php
class RedMallee_Menu_Sub_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 2;
        $ret['cssClass'] .= ' webListNone';
        $ret['generators']['subMenu'] = array(
            'class' => 'Kwc_Menu_Generator',
            'component' => 'RedMallee_Menu_SubSub_Component'
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