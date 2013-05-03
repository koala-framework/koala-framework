<?php
class Kwc_Shop_Cart_OrderProduct extends Kwf_Model_Db_Row
{
    protected function _beforeSave()
    {
        parent::_beforeSave();
        //add_component_id darf leer sein, passiert wenn eine bestellung im backend angelegt wird
        if (!$this->add_component_class) {
            $e = new Kwf_Exception("add_component_class is required");
            $e->logOrThrow();
        }
    }

    public final function getProductPrice()
    {
        return $this->getParentRow('Order')->getProductPrice($this);
    }

    public final function getProductText()
    {
        return $this->getParentRow('Order')->getProductText($this);
    }
}
