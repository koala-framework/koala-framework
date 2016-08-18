<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Paragraphs_Products_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['componentName'] = trlKwfStatic('Order Products');
        return $ret;
    }

    protected function _getOrder()
    {
        $ret = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'))
            ->getReferencedModel('Order')->getCartOrder();
        if (!$ret || !$ret->data) {
            return null;
        }
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

        $order = $this->_getOrder();
        $ret['order'] = $order;
        if ($order) {
            $ret['items'] = $order->getProductsData();
            $c = $this->getData()->getParentByClass('Kwc_Shop_Cart_Checkout_Component');
            $ret['sumRows'] = $c->getComponent()->getSumRows($order);
        }

        $ret['tableFooterText'] = '';
        return $ret;
    }
}
