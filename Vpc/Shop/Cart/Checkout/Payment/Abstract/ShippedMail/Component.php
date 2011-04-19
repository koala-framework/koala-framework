<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_ShippedMail_Component extends Vpc_Mail_Editable_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_Abstract_ShippedMail_Paragraphs_Component';
        $ret['componentName'] = trlVps('Shop Shipped Mail');
        $ret['recipientSources'] = array(
            'ord' => 'Vpc_Shop_Cart_Orders'
        );
        return $ret;
    }

    public function getPlaceholders(Vpc_Mail_Recipient_Interface $o = null)
    {
        $ret = parent::getPlaceholders($o);
        if ($o) {
            $ret = array_merge($ret, $o->getPlaceholders());
        }
        return $ret;
    }
}
