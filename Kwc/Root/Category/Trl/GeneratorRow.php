<?php
class Kwc_Root_Category_Trl_GeneratorRow extends Kwf_Model_Proxy_Row
{
    protected function _beforeInsert()
    {
        parent::_beforeInsert();
        if (!$this->visible) $this->visible = 0;
    }

    protected function _beforeDelete()
    {
        throw new Kwf_ClientException(trlKwf("Can't delete translated pages."));
    }
}
