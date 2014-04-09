<?php
class Kwc_Menu_Expanded_ParentMenu_Trl_Component extends Kwc_Menu_ParentMenu_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach ($ret['menu'] as $k=>$m) {
            $ret['menu'][$k]['submenu'] = $this->getData()->chained->getComponent()->getMenuData($m['data']);
        }
        return $ret;
    }
}
