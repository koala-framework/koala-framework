<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Paragraphs_Component extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs']['component'] = array(
            'textImage' => 'Vpc_TextImage_Component',
            'products' => 'Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Paragraphs_Products_Component'
        );
        return $ret;
    }
}
