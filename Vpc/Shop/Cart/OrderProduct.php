<?php
class Vpc_Shop_Cart_OrderProduct extends Vps_Model_Db_Row
{
    public function getTotal()
    {
        return $this->amount * $this->getParentRow('Product')->price;
    }

}
