<?php
class Vpc_Row extends Vps_Db_Table_Row_Abstract
{
    protected function _postUpdate()
    {
        parent::_postUpdate();
        Vps_Index_Vpc::update($this->component_id);
        Vps_Index_Vpc::clearCache($this->component_id);
    }

    protected function _postInsert()
    {
        parent::_postInsert();
        Vps_Index_Vpc::update($this->component_id);
        Vps_Index_Vpc::clearCache($this->component_id);
    }

    protected function _postDelete()
    {
        parent::_postDelete();
        Vps_Index_Vpc::update($this->component_id);
        Vps_Index_Vpc::clearCache($this->component_id);
    }
}
