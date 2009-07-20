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

    protected function _replacePlaceholders($text, Vpc_Mail_Recipient_Interface $recipient)
    {
        $text = parent::_replacePlaceholders($text, $recipient);
        $text = str_replace('%orderNumber%', $recipient->getOrderNumber(), $text);
        return $text;
    }
}
