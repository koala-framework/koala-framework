<?php
class Kwc_Menu_Trl_Component extends Kwc_Menu_Abstract_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        return $ret;
    }

    public function getMenuData()
    {
        $menu = array();
        $masterMenu = $this->getData()->chained->getComponent()->getMenuData(null, array('ignoreVisible'=>true));
        foreach ($masterMenu as $m) {
            $component = Kwc_Chained_Trl_Component::getChainedByMaster($m['data'], $this->getData());
            if ($component) {
                $m['data'] = $component;
                $m['text'] = '{name}';
                $menu[] = $m;
            }
        }
        $this->_attachEditableToMenuData($menu);
        return $menu;
    }

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
                } else if (is_instance_of($class, 'Kwc_Menu_EditableItems_Trl_Component')) {
                    $c = $menuComponent->getChildComponent('-'.$key);
                    $c->getComponent()->attachEditableToMenuData($menuData);
                }
            }
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['menu'] = $this->getMenuData();
        $ret['subMenu'] = $this->getData()->getChildComponent('-subMenu');
        return $ret;
    }

    public function hasContent()
    {
        $tvars = $this->getTemplateVars();
        return !!count($tvars['menu']);
    }
}
