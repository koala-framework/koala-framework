<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_ShippedMail_Component extends Vpc_Mail_Editable_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_Abstract_ShippedMail_Paragraphs_Component';
        $ret['recipientSources'] = array(
            'ord' => 'Vpc_Shop_Cart_Orders'
        );
        return $ret;
    }

    public function getName()
    {
        return trlVps('Shop Shipped Mail') . ' ' . Vpc_Abstract::getSetting($this->getData()->parent->componentClass, 'componentName');
    }

    public function getPlaceholders(Vpc_Shop_Cart_Order $o)
    {
        $ret = parent::getPlaceholders($o);
        $ret = array_merge($ret, $o->getPlaceholders());
        return $ret;
    }
}
