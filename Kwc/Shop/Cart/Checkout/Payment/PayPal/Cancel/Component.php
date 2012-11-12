<?php
class Kwc_Shop_Cart_Checkout_Payment_PayPal_Cancel_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Kwc_Shop_Cart_Checkout_Payment_PayPal_Cancel_ContentSender';
        return $ret;
    }
}
