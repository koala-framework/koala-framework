<?php
class Kwc_Abstract_Flash_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['placeholder'] = self::getChainedByMaster($ret['placeholder'], $this->getData());
        return $ret;
    }
}
