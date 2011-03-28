<?php
class Vpc_FulltextSearch_Box_Cc_Component extends Vpc_Chained_Cc_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['searchPage'] = Vpc_Chained_Cc_Component::getChainedByMaster($ret['searchPage'], $this->getData(), array('ignoreVisible'=>true));
        return $ret;
    }
}
