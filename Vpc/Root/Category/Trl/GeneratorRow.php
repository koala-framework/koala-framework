<?php
class Vpc_Root_Category_Trl_GeneratorRow extends Vps_Model_Proxy_Row
{
    protected function _beforeInsert()
    {
        parent::_beforeInsert();
        if (!$this->visible) $this->visible = 0;
    }

    protected function _beforeDelete()
    {
        throw new Vps_ClientException(trlVps("Can't delete translated pages."));
    }
}
