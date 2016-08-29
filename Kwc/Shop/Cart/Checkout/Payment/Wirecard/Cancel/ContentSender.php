<?php
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_Cancel_ContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function sendContent($includeMaster)
    {
        $session = new Kwf_Session_Namespace('kwcShopCart');
        if ($session->wirecardCartId) {
            Kwc_Shop_Cart_Orders::setCartOrderId($session->wirecardCartId);
            $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->_data->parent->parent->parent->componentClass, 'childModel'))
                ->getReferencedModel('Order')->getCartOrder();
            $db = Kwf_Registry::get('db');
            $db->query(
                "UPDATE `kwc_shop_orders` SET `status` = 'cart' WHERE `id` = {$db->quote($order->id)} AND `status` = 'processing'"
            );
            unset($session->wirecardCartId);
        }
        Kwf_Util_Redirect::redirect($this->_data->parent->parent->parent->getUrl());
    }
}
