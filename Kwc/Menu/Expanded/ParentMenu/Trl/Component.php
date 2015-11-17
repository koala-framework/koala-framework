<?php
class Kwc_Menu_Expanded_ParentMenu_Trl_Component extends Kwc_Menu_ParentMenu_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        foreach ($ret['menu'] as $k=>$m) {
            $ret['menu'][$k]['submenu'] = array();
            $masterSubMenu = $this->getData()->chained->getComponent()->getMenuData($m['data']->chained, array('ignoreVisible'=>true), 'Kwc_Menu_Expanded_EditableItems_Component');
            foreach ($masterSubMenu as $sm) {
                $sComponent = Kwc_Chained_Trl_Component::getChainedByMaster($sm['data'], $this->getData());
                if ($sComponent) {
                    $sm['data'] = $sComponent;
                    $sm['text'] = $sComponent->name;
                    $ret['menu'][$k]['submenu'][] = $sm;
                }
            }
            $this->_attachEditableToMenuData($ret['menu'][$k]['submenu']);
        }
        return $ret;
    }
}
