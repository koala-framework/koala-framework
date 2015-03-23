<?php
class Kwc_Trl_Domains_Domain_MasterGenerator extends Kwc_Chained_Trl_MasterGenerator
{
    protected function _getLanguageRow($parentData)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('domain', $parentData->id);
        $s->whereEquals('master', 1);
        return $this->_getModel()->getRow($s);
    }
}
