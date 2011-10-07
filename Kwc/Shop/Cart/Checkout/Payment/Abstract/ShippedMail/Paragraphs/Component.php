<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_ShippedMail_Paragraphs_Component
    extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs']['component']['address'] = 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Address_Component';
        $ret['generators']['paragraphs']['component']['products'] = 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Products_Component';
        return $ret;
    }
}
