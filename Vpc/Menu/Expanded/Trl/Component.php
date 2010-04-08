<?php
class Vpc_Menu_Expanded_Trl_Component extends Vpc_Menu_Abstract_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $menu = array();
        foreach ($ret['menu'] as $m) {
            $component = $this->_getChainedComponent($m);
            if ($component) {
                $component->submenu = array();
                foreach ($m->submenu as $sm) {
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
