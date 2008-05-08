<?php
class Vpc_Row extends Vps_Db_Table_Row_Abstract
{
    protected function _postUpdate()
    {
        parent::_postUpdate();
        Vps_Index_Vpc::update($this->component_id);
        Vpc_Admin::getInstance($this->getTable()->getComponentClass())
            ->clearCache($this);
    }

    protected function _postInsert()
    {
        parent::_postInsert();
        Vps_Index_Vpc::update($this->component_id);
        Vpc_Admin::getInstance($this->getTable()->getComponentClass())
            ->clearCache($this);
    }

    protected function _postDelete()
    {
        parent::_postDelete();
        Vps_Index_Vpc::update($this->component_id);
        Vpc_Admin::getInstance($this->getTable()->getComponentClass())
            ->clearCache($this);
    }
}
