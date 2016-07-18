<?php
class Kwc_Shop_Cart_Checkout_Payment_PayPal_Confirm_Component extends Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component
{
    public function processInput($data)
    {
        $custom = isset($data['custom']) ? rawurldecode($data['custom']) : null;
        $data = Kwf_Util_PayPal_Ipn_LogModel::decodeCallback($custom);
        if ($data) {
            $db = Kwf_Registry::get('db');

            $date = date('Y-m-d H:i:s');
            $sql = "UPDATE `kwc_shop_orders` SET
              `payment_component_id` = {$db->quote($this->getData()->parent->componentId)},
              `checkout_component_id` = {$db->quote($this->getData()->parent->parent->componentId)},
              `cart_component_class` = {$db->quote($this->getData()->parent->parent->parent->componentClass)},
              `date` = {$db->quote($date)},
              `status` = 'ordered'
              WHERE `id` = {$db->quote($data['data']['orderId'])} AND (`status` = 'processing' OR `status` = 'cart')";
            $db->query($sql);

            Kwc_Shop_Cart_Orders::setOverriddenCartOrderId($data['data']['orderId']);
            if (Kwc_Shop_Cart_Orders::getCartOrderId() == $data['data']['orderId']) {
                Kwc_Shop_Cart_Orders::resetCartOrderId();
            }
        }
    }
}
