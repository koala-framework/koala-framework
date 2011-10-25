<?php
class Kwc_Shop_Cart_Checkout_Payment_PayPal_Confirm_Component extends Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component
{
    public function processInput($data)
    {
        $data = Kwf_Util_PayPal_Ipn_LogModel::decodeCallback($data['custom']);
        if ($data) {
            $order = Kwf_Model_Abstract::getInstance('Kwc_Shop_Cart_Orders')->getRow($data['data']['orderId']);
            $order->date = date('Y-m-d H:i:s');
            if ($order->status == 'cart') $order->status = 'ordered';
            $order->save();
            Kwc_Shop_Cart_Orders::setOverriddenCartOrderId($order->id);
            if (Kwc_Shop_Cart_Orders::getCartOrderId() == $order->id) {
                Kwc_Shop_Cart_Orders::resetCartOrderId();
            }
        }
    }
}
