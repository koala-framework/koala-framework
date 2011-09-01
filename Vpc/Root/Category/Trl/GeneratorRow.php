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

    protected function _beforeUpdate()
    {
        parent::_beforeUpdate();
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($this->component_id, array('ignoreVisible'=>true, 'limit'=>1));
        if ($c->isHome && !$this->visible) {
            throw new Vps_ClientException(trlVps('Cannot set Home Page invisible'));
        }
    }
}
