<?php
class Vpc_Shop_Cart_Checkout_OrderProductsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_modelName = 'Vpc_Shop_Cart_OrderProducts';
    protected $_paging = 30;
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('product', trlVps('Product')))
            ->setData(new Vps_Data_Table_Parent('Product'));
        $this->_columns->add(new Vps_Grid_Column('amount', trlVps('Amount'), 50));
        $this->_columns->add(new Vps_Grid_Column('size', trlVps('Size'), 100));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('shop_order_id', $this->_getParam('id'));
        return $ret;
    }
}
