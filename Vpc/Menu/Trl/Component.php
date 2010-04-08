<?php
class Vpc_Menu_Trl_Component extends Vpc_Menu_Abstract_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $menu = array();
        foreach ($ret['menu'] as $m) {
            $component = $this->_getChainedComponent($m);
            if ($component) $menu[] = $component;
        }
        $ret['menu'] = $menu;
        $ret['subMenu'] = $this->getData()->getChildComponent('-subMenu');
        return $ret;
    }
}
