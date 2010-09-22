<?php
class Vpc_Shop_Cart_OrderProduct extends Vps_Model_Db_Row
{
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->add_component_id) {
            $e = new Vps_Exception("add_component_id is required");
            $e->logOrThrow();
        }
        if (!$this->add_component_class) {
            $e = new Vps_Exception("add_component_class is required");
            $e->logOrThrow();
        }
    }
}
