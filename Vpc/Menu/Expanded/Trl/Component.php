<?php
class Vpc_Menu_Expanded_Trl_Component extends Vpc_Menu_Abstract_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $menu = array();
        $masterMenu = $this->getData()->chained->getComponent()->getMenuData(null, array('ignoreVisible'=>true));
        foreach ($masterMenu as $m) {
            $component = $this->_getChainedComponent($m['data']);
            if ($component) {
                $m['submenu'] = array();
                $masterSubMenu = $this->getData()->chained->getComponent()->getMenuData($m['data'], array('ignoreVisible'=>true));
                foreach ($masterSubMenu as $sm) {
                    $sComponent = $this->_getChainedComponent($sm['data']);
                    if ($sComponent) {
                        $sm['data'] = $sComponent;
                        $sm['text'] = $sComponent->name;
                        $m['submenu'][] = $sm;
                    }
                }
                $m['data'] = $component;
                $m['text'] = $component->name;
                $menu[] = $m;
            }
        }
        $ret['menu'] = $menu;
        return $ret;
    }
}
