<?php
class Vpc_Shop_Cart_Generator extends Vps_Component_Generator_Table
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;

        $session = new Zend_Session_Namespace('vpcShopCart');
        if (!$session->orderId) return null;
        $ret->whereEquals('shop_order_id', Vpc_Shop_Cart_Orders::getCartOrderId());

        return $ret;
    }

}
