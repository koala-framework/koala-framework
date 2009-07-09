<?php
class Vpc_Shop_Cart_Checkout_Payment_PayPal_Confirm_Component extends Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component
{
    public function processInput($data)
    {
        $data = Vps_Util_PayPal_Ipn_LogModel::decodeCallback($data['custom']);
        if ($data) {
            $order = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getRow($data['data']['orderId']);

            //abfrage manuell machen damit nicht status auf ordered zurückgesetzt wird wenn ipn bereits auf payed gesetzt hat
            Vps_Registry::get('db')->query("
                UPDATE vpc_shop_orders SET status='ordered', date=NOW()
                WHERE id='$order->id' AND status='cart'
            ");
            Vpc_Shop_Cart_Orders::resetCartOrderId();
        }
    }
}
