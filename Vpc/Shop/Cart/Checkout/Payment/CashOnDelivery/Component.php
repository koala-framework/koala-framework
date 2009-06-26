<?php
class Vpc_Shop_Cart_Checkout_Payment_CashOnDelivery_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Cash on Delivery');
        $ret['cashOnDeliveryCharge'] = 6.5;
        return $ret;
    }
    public function getAdditionalSumRows($order)
    {
        $ret = parent::getAdditionalSumRows($order);
        $ret[] = array(
            'text' => trlVps('Cash on Delivery Charge').':',
            'amount' => $this->getCashOnDeliveryCharge($order)
        );
        return $ret;
    }

    public function getCashOnDeliveryCharge($order)
    {
        return $this->_getSetting('cashOnDeliveryCharge');
    }
}
