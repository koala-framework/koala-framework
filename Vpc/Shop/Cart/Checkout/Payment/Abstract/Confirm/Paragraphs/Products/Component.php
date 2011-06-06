<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Paragraphs_Products_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['componentName'] = trlVps('Order Products');
        return $ret;
    }

    protected function _getOrder()
    {
        $ret = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getCartOrder();
        if (!$ret || !$ret->data) {
            return null;
        }
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $order = $this->_getOrder();
        if ($order) {
            $ret['items'] = $order->getProductsData();
            $c = $this->getData()->getParentByClass('Vpc_Shop_Cart_Checkout_Component');
            $ret['sumRows'] = $c->getComponent()->getSumRows($order);
        }
        return $ret;
    }

}
