<?php
class Vpc_Shop_Cart_Checkout_Payment_PayPal_Mail_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] .= ' '.trlVps('PayPal');
        return $ret;
    }
}
