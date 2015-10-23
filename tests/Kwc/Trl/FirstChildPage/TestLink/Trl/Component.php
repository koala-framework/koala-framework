<?php
class Kwc_Trl_FirstChildPage_TestLink_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['link1'] = Kwc_Chained_Trl_Component::getChainedByMaster($ret['link1'], $this->getData());
        $ret['link4'] = Kwc_Chained_Trl_Component::getChainedByMaster($ret['link4'], $this->getData());
        $ret['link5'] = Kwc_Chained_Trl_Component::getChainedByMaster($ret['link5'], $this->getData());
        return $ret;
    }
}
