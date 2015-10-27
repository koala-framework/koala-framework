<?php
class Kwc_Menu_Expanded_ParentMenu_Cc_Component extends Kwc_Menu_ParentMenu_Cc_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        foreach ($ret['menu'] as $k=>$m) {
            $masterSubMenu = $this->getData()->chained->getComponent()
                ->getMenuData($m['data']
                ->chained, array('ignoreVisible'=>true), 'Kwc_Menu_Expanded_EditableItems_Component');
            if (count($masterSubMenu)) {
                $ret['menu'][$k]['submenu'] = $masterSubMenu;
            }
        }
        return $ret;
    }
}
