<?php
class Kwc_Shop_Cart_Checkout_Payment_CashOnDelivery_Component extends Kwc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Cash on Delivery');
        $ret['cashOnDeliveryCharge'] = 6.5;
        $ret['orderData'] = 'Kwc_Shop_Cart_Checkout_Payment_CashOnDelivery_OrderData';
        return $ret;
    }
}
