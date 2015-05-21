<?php
class Kwc_Shop_Cart_Checkout_Payment_PayPal_Confirm_Component extends Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component
{
    public function processInput($data)
    {
        $custom = isset($data['custom']) ? rawurldecode($data['custom']) : null;
        $data = Kwf_Util_PayPal_Ipn_LogModel::decodeCallback($custom);
        if ($data) {
            $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->parent->parent->parent->componentClass, 'childModel'))
                ->getReferencedModel('Order')->getRow($data['data']['orderId']);
            if ($order->status == 'processing' || $order->status == 'cart') {
                $order->payment_component_id = $this->getData()->parent->componentId;
                $order->checkout_component_id = $this->getData()->parent->parent->componentId;
                $order->cart_component_class = $this->getData()->parent->parent->parent->componentClass;
                $order->date = date('Y-m-d H:i:s');
                $order->status = 'ordered';
                $order->save();
            }
            Kwc_Shop_Cart_Orders::setOverriddenCartOrderId($order->id);
            if (Kwc_Shop_Cart_Orders::getCartOrderId() == $order->id) {
                Kwc_Shop_Cart_Orders::resetCartOrderId();
            }
        }
    }
}
