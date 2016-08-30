<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_ShippedMail_Paragraphs_Component
    extends Kwc_Paragraphs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['paragraphs']['component'] = array(
            'textImage' => 'Kwc_TextImage_Component',
            'address' => 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Address_Component',
            'products' => 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Products_Component'
        );
        return $ret;
    }
}
