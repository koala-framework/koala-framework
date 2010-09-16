<?php
class Vpc_Shop_Cart_Checkout_Payment_CashOnDelivery_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Cash on Delivery');
        $ret['generators']['mail']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_CashOnDelivery_Mail_Component';
        $ret['generators']['confirm']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_CashOnDelivery_Confirm_Component';
        $ret['generators']['shippedMail']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_CashOnDelivery_ShippedMail_Component';
        $ret['cashOnDeliveryCharge'] = 6.5;
        $ret['orderData'] = 'Vpc_Shop_Cart_Checkout_Payment_CashOnDelivery_OrderData';
        return $ret;
    }

    protected final function _getCashOnDeliveryCharge($order)
    {
        throw new Vps_Exception('gibts nimma');
    }
}
