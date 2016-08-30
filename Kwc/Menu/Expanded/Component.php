<?php
class Kwc_Menu_Expanded_Component extends Kwc_Menu_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
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

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

        $ret['menu'] = $this->_getMenuData();
        foreach ($ret['menu'] as $k=>$m) {
            $ret['menu'][$k]['submenu'] = $this->_getMenuData($m['data'], array(), 'Kwc_Menu_Expanded_EditableItems_Component');
            $ret['menu'][$k]['isFirstChildPage'] = is_instance_of($m['data']->componentClass, 'Kwc_Basic_LinkTag_FirstChildPage_Data');
        }
        $ret['level'] = $this->_getSetting('level');

        return $ret;
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
