<?php
class Kwc_Menu_Expanded_Trl_Component extends Kwc_Menu_Abstract_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $menu = array();
        $masterMenu = $this->getData()->chained->getComponent()->getMenuData(null, array('ignoreVisible'=>true));
        foreach ($masterMenu as $m) {
            $component = Kwc_Chained_Trl_Component::getChainedByMaster($m['data'], $this->getData());
            if ($component) {
                $m['submenu'] = array();
                $masterSubMenu = $this->getData()->chained->getComponent()->getMenuData($m['data'], array('ignoreVisible'=>true));
                foreach ($masterSubMenu as $sm) {
                    $sComponent = Kwc_Chained_Trl_Component::getChainedByMaster($sm['data'], $this->getData());
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
