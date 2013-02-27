<?php
abstract class Kwc_Menu_Abstract_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['parentPage'] = self::getChainedByMaster($ret['parentPage'], $this->getData());
        return $ret;
    }
}
