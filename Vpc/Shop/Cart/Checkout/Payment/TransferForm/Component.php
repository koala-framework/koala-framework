<?php
class Vpc_Shop_Cart_Checkout_Payment_TransferForm_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Transfer Form');
        $ret['generators']['mail']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_TransferForm_Mail_Component';
        $ret['generators']['confirm']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_TransferForm_Confirm_Component';
        $ret['generators']['shippedMail']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_TransferForm_ShippedMail_Component';
        return $ret;
    }
}
