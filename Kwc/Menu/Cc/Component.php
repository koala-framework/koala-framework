<?php
class Kwc_Menu_Cc_Component extends Kwc_Menu_Abstract_Cc_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $menu = array();
        $masterMenu = $this->getData()->chained->getComponent()->getMenuData(null, array('ignoreVisible'=>true));
        foreach ($masterMenu as $m) {
            $component = self::getChainedByMaster($m['data'], $this->getData());
            if ($component) {
                $m['data'] = $component;
                $m['text'] = '{name}';
                $menu[] = $m;
            }
        }
        $ret['menu'] = $menu;
        $ret['subMenu'] = $this->getData()->getChildComponent('-subMenu');
        return $ret;
    }

    public function hasContent()
    {
        $tvars = $this->getTemplateVars();
        return !!count($tvars['menu']);
    }
}
