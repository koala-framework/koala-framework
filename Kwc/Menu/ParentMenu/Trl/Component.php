<?php
class Kwc_Menu_ParentMenu_Trl_Component extends Kwc_Menu_Trl_Component
{
    protected function _attachEditableToMenuData(&$menuData, $menuComponent = null)
    {
        $component = $this->getData()->chained->getComponent()->getParentContentData();
        $component = Kwc_Chained_Trl_Component::getChainedByMaster($component, $this->getData());
        parent::_attachEditableToMenuData($menuData, $component);
    }
}
