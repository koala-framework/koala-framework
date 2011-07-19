<?php
class Vpc_Shop_Cart_Checkout_Payment_None_Mail_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] .= ' '.trlVps('None');
        return $ret;
    }
}
