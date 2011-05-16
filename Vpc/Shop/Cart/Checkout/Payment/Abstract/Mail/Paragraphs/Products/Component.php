<?php
//TODO: könnte von Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Paragraphs_Products_Component erben
class Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Products_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['componentName'] = trlVps('Order Products');
        return $ret;
    }

    public function getMailVars(Vpc_Shop_Cart_Order $order)
    {
        $ret = parent::getMailVars($order);

        $ret['items'] = $order->getProductsData();

        $c = $this->getData()->getParentByClass('Vpc_Shop_Cart_Checkout_Component');
        $ret['sumRows'] = $c->getComponent()->getSumRows($order);

        return $ret;
    }
}
