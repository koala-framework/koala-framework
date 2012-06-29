<?php
class Kwc_Menu_Abstract_Cc_Component extends Kwc_Chained_Cc_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['parentPage'] = self::getChainedByMaster($ret['parentPage'], $this->getData());
        return $ret;
    }
}
