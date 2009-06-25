<?php
class Vpc_Shop_Cart_Checkout_Payment_PayPal_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('PayPal');
        $ret['generators']['child']['component']['confirmLink'] = 'Vpc_Shop_Cart_Checkout_Payment_PayPal_ConfirmLink_Component';
        $ret['generators']['confirm']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_PayPal_Confirm_Component';
        $ret['generators']['confirm']['name'] = trlVps('done');
        return $ret;
    }

}
