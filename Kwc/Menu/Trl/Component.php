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
        return $menu;
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
