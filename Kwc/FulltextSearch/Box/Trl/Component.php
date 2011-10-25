<?php
class Kwc_FulltextSearch_Box_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['searchPage'] = Kwc_Chained_Trl_Component::getChainedByMaster($ret['searchPage'], $this->getData(), array('ignoreVisible'=>true));
        return $ret;
    }
}