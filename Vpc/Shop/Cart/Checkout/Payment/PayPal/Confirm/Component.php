<?php
class Vpc_Shop_Cart_Checkout_Payment_PayPal_Confirm_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component
{
    public function processInput($data)
    {
        $data = Vps_Util_PayPal_Ipn_LogModel::decodeCallback($data['custom']);
        if ($data) {
            $order = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getRow($data['data']['orderId']);
            $order->date = date('Y-m-d H:i:s');
            if ($order->status == 'cart') $order->status = 'ordered';
            $order->save();
            Vpc_Shop_Cart_Orders::setOverriddenCartOrderId($order->id);
            if (Vpc_Shop_Cart_Orders::getCartOrderId() == $order->id) {
                Vpc_Shop_Cart_Orders::resetCartOrderId();
            }
        }
    }
}
