<?php
class Vpc_Menu_Expanded_Trl_Component extends Vpc_Menu_Abstract_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $menu = array();
        $masterMenu = $this->getData()->chained->getComponent()->getMenuData(null, array('ignoreVisible'=>true));
        foreach ($masterMenu as $m) {
            $component = $this->_getChainedComponent($m);
            if ($component) {
                $component->submenu = array();
                $masterSubMenu = $this->getData()->chained->getComponent()->getMenuData($m, array('ignoreVisible'=>true));
                foreach ($masterSubMenu as $sm) {
                    $sComponent = $this->_getChainedComponent($sm);
                    if ($sComponent) {
                        $component->submenu[] = $sComponent;
                    }
                }
                $menu[] = $component;
            }
        }
        $ret['menu'] = $menu;
        return $ret;
    }
}
