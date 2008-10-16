<?php
class Vpc_Shop_Cart_Order extends Vps_Model_Db_Row
{
    protected function _afterSave()
    {
        parent::_afterSave();
        if (Vpc_Shop_Cart_Orders::getCartOrderId() == $this->id && $this->status != 'cart') {
            Vpc_Shop_Cart_Orders::resetCartOrderId();
        }
    }

    public function getShipping()
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_Shop_Cart_Component')
            ->getComponent()->getShipping();
    }

    public function getSubTotal()
    {
        $ret = 0;
        foreach ($this->getChildRows('Products') as $op) {
            $p = $op->getParentRow('Product');
            $ret += $p->price * $op->amount;
        }
        return $ret;
    }

    public function getTotalAmount()
    {
        $ret = 0;
        foreach ($this->getChildRows('Products') as $op) {
            $ret += $op->amount;
        }
        return $ret;
    }

    public function getTotal()
    {
        return $this->getShipping() + $this->getSubTotal();
    }
}
