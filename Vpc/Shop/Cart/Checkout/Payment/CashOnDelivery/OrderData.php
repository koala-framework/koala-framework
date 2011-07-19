<?php
class Vpc_Shop_Cart_Checkout_Payment_CashOnDelivery_OrderData extends Vpc_Shop_Cart_Checkout_Payment_Abstract_OrderData
{
    public function getAdditionalSumRows(Vpc_Shop_Cart_Order $order)
    {
        $ret = parent::getAdditionalSumRows($order);
        $ret[] = array(
            'class' => 'cashOnDelivery',
            'text' => trlVps('Cash on Delivery Charge').':',
            'amount' => $this->_getCashOnDeliveryCharge($order)
        );
        return $ret;
    }

    protected function _getCashOnDeliveryCharge($order)
    {
        return Vpc_Abstract::getSetting($this->_class, 'cashOnDeliveryCharge');
    }
}
