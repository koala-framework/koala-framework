<?php
class Kwc_Trl_FirstChildPage_TestLink_Component extends Kwc_Abstract
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['link1'] = Kwf_Component_Data_Root::getInstance()->getComponentById(1);
        $ret['link4'] = Kwf_Component_Data_Root::getInstance()->getComponentById(4);
        $ret['link5'] = Kwf_Component_Data_Root::getInstance()->getComponentById(5);
        return $ret;
    }
}
