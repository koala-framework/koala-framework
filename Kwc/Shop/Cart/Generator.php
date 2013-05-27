<?php
class Kwc_Shop_Cart_Generator extends Kwf_Component_Generator_Table
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;

        $session = new Kwf_Session_Namespace('kwcShopCart');
        if (!$session->orderId) return null;
        $ret->whereEquals('shop_order_id', Kwc_Shop_Cart_Orders::getCartOrderId());

        return $ret;
    }

    protected function _getParentDataByRow($row, $select)
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentByClass($this->_class, array('limit'=>1));
    }
}
