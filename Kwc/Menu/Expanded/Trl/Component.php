<?php
class Kwc_Menu_Expanded_Trl_Component extends Kwc_Menu_Abstract_Trl_Component
{
    protected function _attachEditableToMenuData(&$menuData, $menuComponent = null)
    {
        if (!$menuComponent) $menuComponent = $this->getData();
        foreach (Kwc_Abstract::getSetting($menuComponent->componentClass, 'generators') as $key => $generator) {
            $componentClasses = $generator['component'];
            if (!is_array($componentClasses)) $componentClasses = array($componentClasses);
            foreach ($componentClasses as $class) {
                if (is_instance_of($class, 'Kwc_Menu_Expanded_EditableItems_Trl_Component')) {
                    $c = $menuComponent->getChildComponent('-'.$key);
                    $c->getComponent()->attachEditableToMenuData($menuData);
                }
            }
        }
    }

    public function getMenuData()
    {
        return $this->getData()->chained->getComponent()->getMenuData();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $menu = array();
        $masterMenu = $this->getData()->chained->getComponent()->getMenuData(null, array('ignoreVisible'=>true));
        foreach ($masterMenu as $m) {
            $component = Kwc_Chained_Trl_Component::getChainedByMaster($m['data'], $this->getData());
            if ($component) {
                $m['submenu'] = array();
                $masterSubMenu = $this->getData()->chained->getComponent()->getMenuData($m['data'], array('ignoreVisible'=>true), 'Kwc_Menu_Expanded_EditableItems_Component');
                foreach ($masterSubMenu as $sm) {
                    $sComponent = Kwc_Chained_Trl_Component::getChainedByMaster($sm['data'], $this->getData());
                    if ($sComponent) {
                        $sm['data'] = $sComponent;
                        $sm['text'] = $sComponent->name;
                        $m['submenu'][] = $sm;
                    }
                }
                $this->_attachEditableToMenuData($m['submenu']);
                $m['data'] = $component;
                $m['text'] = $component->name;
                $menu[] = $m;
            }
        }
        $ret['menu'] = $menu;
        return $ret;
    }
}
