<?php
class Vpc_Basic_Text_StylesRow extends Vps_Model_Proxy_Row
{
    public function __toString()
    {
        return $this->name;
    }
    protected function _afterSave()
    {
        parent::_afterSave();
        $this->getModel()->removeCache();
    }
    protected function _afterDelete()
    {
        parent::_afterDelete();
        $this->getModel()->removeCache();
    }
}
