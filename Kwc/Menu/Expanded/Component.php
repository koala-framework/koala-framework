<?php
class Kwc_Menu_Expanded_Component extends Kwc_Menu_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['menuModel'] = 'Kwc_Menu_Expanded_MenuModel';
        return $ret;
    }

    public static function getAlternativeComponents($componentClass)
    {
        $ret = parent::getAlternativeComponents($componentClass);
        $ret['parentMenu'] = 'Kwc_Menu_Expanded_ParentMenu_Component.'.$componentClass;
        return $ret;
    }

    protected static function _requiredLevels($componentClass)
    {
        $level = (int)Kwc_Abstract::getSetting($componentClass, 'level');
        return $level + 2;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['menu'] = $this->_getMenuData();
        $this->_attachEditableToMenuData($ret['menu']);
        foreach ($ret['menu'] as $k=>$m) {
            $ret['menu'][$k]['submenu'] = $this->_getMenuData($m['data']);
            $this->_attachExpandedEditableToMenuData($ret['menu'][$k]['submenu']);
        }
        $ret['level'] = $this->_getSetting('level');

        return $ret;
    }

    protected function _attachExpandedEditableToMenuData(&$menuData)
    {
        foreach ($this->_getSetting('generators') as $key => $generator) {
            if (is_instance_of($generator['component'], 'Kwc_Menu_Expanded_EditableItems_Component')) {
                $c = $this->getData()->getChildComponent('-'.$key);
                $c->getComponent()->attachEditableToMenuData($menuData);
            }
        }
    }

    public function hasContent()
    {
        $c = count($this->_getMenuData());
        if ($c > 0) {
            return true;
        }
        return false;
    }

}
