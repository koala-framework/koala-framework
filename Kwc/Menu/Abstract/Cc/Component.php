<?php
class Kwc_Menu_Abstract_Cc_Component extends Kwc_Chained_Cc_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['parentPage'] = self::getChainedByMaster($ret['parentPage'], $this->getData());
        return $ret;
    }
}
