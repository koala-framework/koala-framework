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

    protected function _beforeUpdate()
    {
        parent::_beforeUpdate();
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->component_id, array('ignoreVisible'=>true, 'limit'=>1));
        if ($c->isHome && !$this->visible) {
            throw new Kwf_ClientException(trlKwf('Cannot set Home Page invisible'));
        }
    }
}
