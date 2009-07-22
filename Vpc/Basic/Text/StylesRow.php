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
        Vpc_Basic_Text_StylesModel::removeCache();
    }
    protected function _afterDelete()
    {
        parent::_afterDelete();
        Vpc_Basic_Text_StylesModel::removeCache();
    }
}
