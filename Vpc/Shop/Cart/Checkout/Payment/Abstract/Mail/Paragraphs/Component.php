<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Component
    extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs']['component'] = array(
            'textImage' => 'Vpc_TextImage_Component',
            'address' => 'Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Address_Component',
            'products' => 'Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Products_Component',
            'message' => 'Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Message_Component'
        );
        return $ret;
    }
}
