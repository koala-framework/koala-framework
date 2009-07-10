<?php
class Vpc_Shop_Cart_Checkout_Payment_PrePayment_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Pre Payment');
        $ret['generators']['mail']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_PrePayment_Mail_Component';
        return $ret;
    }
}
