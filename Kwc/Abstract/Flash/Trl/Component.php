<?php
class Kwc_Abstract_Flash_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['placeholder'] = self::getChainedByMaster($ret['placeholder'], $this->getData());
        return $ret;
    }
}
