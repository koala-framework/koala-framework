<?php
class Vpc_Basic_Text_StylesRow extends Vps_Model_Db_Row
{
    public function __toString()
    {
        return $this->name;
    }
    protected function _postUpdate()
    {
        parent::_postUpdate();
        Vpc_Basic_Text_StylesModel::removeCache();
    }
    protected function _postInsert()
    {
        parent::_postInsert();
        Vpc_Basic_Text_StylesModel::removeCache();
    }
    protected function _postDelete()
    {
        parent::_postDelete();
        Vpc_Basic_Text_StylesModel::removeCache();
    }
}
