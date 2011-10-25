<?php
class Kwc_Shop_Cart_Generator extends Kwf_Component_Generator_Table
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;

        $session = new Zend_Session_Namespace('kwcShopCart');
        if (!$session->orderId) return null;
        $ret->whereEquals('shop_order_id', Kwc_Shop_Cart_Orders::getCartOrderId());

        return $ret;
    }
}
