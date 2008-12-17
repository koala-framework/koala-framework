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
            ->getComponentByClass(
                'Vpc_Shop_Cart_Component',
                array('subroot' => $this->getData())
            )
            ->getComponent()->getShipping($this);
    }

    public function getCashOnDeliveryCharge()
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Vpc_Shop_Cart_Component',
                array('subroot' => $this->getData())
            )
            ->getComponent()->getCashOnDeliveryCharge($this);
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
        return $this->getShipping() + $this->getCashOnDeliveryCharge() + $this->getSubTotal();

    }

    public function getOrderNumber()
    {
        return $this->id + 11000;
    }

    public function getSalutation()
    {
        $ret = '';
        if ($this->sex == 'male') {
            $ret .= trlVps('Mr.');
        } else {
            $ret .= trlVps('Mrs.');
        }
        $ret .= ' '.trim($this->title.' '.$this->lastname);
        return $ret;
    }
}
