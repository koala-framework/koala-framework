<?php
class Vpc_Shop_Cart_Checkout_Payment_TransferForm_ShippedMail_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_ShippedMail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] .= ' '.trlVps('Transfer Form');
        return $ret;
    }
}
