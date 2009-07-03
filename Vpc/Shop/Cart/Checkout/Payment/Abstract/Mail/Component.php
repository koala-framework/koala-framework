<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Component extends Vpc_Mail_Editable_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Component';
        $ret['componentName'] = trlVps('Shop Conformation Mail');
        return $ret;
    }
}
