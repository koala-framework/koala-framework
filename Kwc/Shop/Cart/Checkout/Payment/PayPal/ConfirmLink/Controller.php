<?php
class Kwc_Shop_Cart_Checkout_Payment_PayPal_ConfirmLink_Controller extends Zend_Controller_Action
{
    // is called by js, so it might be that this code isn't called at all
    public function jsonConfirmOrderAction()
    {
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_getParam('paymentComponentId'));
        $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($component->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'))
            ->getReferencedModel('Order')->getCartOrder();
        if ($order && $component &&
            is_instance_of($component->componentClass, 'Kwc_Shop_Cart_Checkout_Payment_PayPal_Component')
        ) {
            $order->payment_component_id = $component->componentId;
            $order->checkout_component_id = $component->parent->componentId;
            $order->cart_component_class = $component->parent->parent->componentClass;
            $order->status = 'ordered';
            $order->date = date('Y-m-d H:i:s');
            $order->save();
            $session = new Kwf_Session_Namespace('kwcShopCart');
            $session->paypalCartId = $order->id;
        }
    }
}
