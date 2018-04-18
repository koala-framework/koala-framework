<?php
class Kwc_Menu_DropdownMask_Component extends Kwc_Menu_Dropdown_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['rootElementClass'] .= ' kwfUp-webListNone';
        // Define the mask parent node, if parent is not body, parents css position has to be "relative"
        $ret['maskParent'] = 'body';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['config'] = array(
            'maskParent' => $this->_getSetting('maskParent')
        );
        return $ret;
    }

    protected function _getMenuData($parentData = null, $select = array(), $editableClass = 'Kwc_Menu_EditableItems_Component')
    {
        $ret = parent::_getMenuData($parentData, $select, $editableClass);
        foreach ($ret as $k=>$i) {
            if (count($ret[$k]['data']->getChildPages(array('showInMenu'=>true))) > 0) {
                $ret[$k]['class'] .= ' hasSubmenu';
            }
        }
        return $ret;
    }
}
