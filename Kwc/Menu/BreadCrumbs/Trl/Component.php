<?php
class Kwc_Menu_BreadCrumbs_Trl_Component extends Kwc_Menu_Abstract_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $links = array();
        foreach ($ret['links'] as $m) {
            $links[] = self::getChainedByMaster($m, $this->getData());
        }
        $ret['links'] = $links;
        return $ret;
    }
}
