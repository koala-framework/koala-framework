<?php
class Kwc_Shop_Cart_Checkout_Payment_PayPal_ConfirmLink_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['paypalButton'] = $this->_getPaypalButton();
        $ret['options'] = array(
            'controllerUrl' =>
                Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl() .
                '/json-confirm-order',
            'params' => array(
                'paymentComponentId' => $this->getData()->parent->componentId
            )
        );
        return $ret;
    }

    protected function _getPaypalButton()
    {
        $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting(
            $this->getData()->chained->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'
        ))->getReferencedModel('Order')->getCartOrder();
        $total = $this->getData()->chained->getParentByClass('Kwc_Shop_Cart_Checkout_Component')
            ->getComponent()->getTotal($order);

        $payment = $this->getData()->getParentByClass('Kwc_Shop_Cart_Checkout_Payment_PayPal_Trl_Component');

        $custom = Kwf_Util_PayPal_Ipn_LogModel::getEncodedCallback(
            $payment->componentId, array('orderId' => $order->id)
        );
        $params = array(
            'amount' => $total,
            'currency_code' => 'EUR',
            'no_shipping' => Kwc_Abstract::getSetting($payment->componentClass, 'noShipping'),
            'custom' => $custom
        );

        return Kwc_Shop_Cart_Checkout_Payment_PayPal_ConfirmLink_Component::buildPayPalButtonHtml($params, $payment, $order);
    }
}

