<?php
class Vpc_Shop_Cart_Order extends Vps_Db_Table_Row
{
    protected function _postUpdate()
    {
        parent::_postUpdate();
        if (Vpc_Shop_Cart_Orders::getCartOrderId() == $this->id && $this->status != 'cart') {
            Vpc_Shop_Cart_Orders::resetCartOrderId();
        }
    }
}
