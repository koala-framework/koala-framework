<?php
class Vpc_Shop_Cart_Checkout_Payment_None_Confirm_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] .= ' '.trlVps('None');
        return $ret;
    }
}
