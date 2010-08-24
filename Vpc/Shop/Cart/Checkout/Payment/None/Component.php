<?php
class Vpc_Shop_Cart_Checkout_Payment_None_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('None');
        $ret['generators']['mail']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_None_Mail_Component';
        $ret['generators']['confirm']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_None_Confirm_Component';
        $ret['generators']['shippedMail']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_None_ShippedMail_Component';
        return $ret;
    }

}
