<?php
class Vpc_Shop_Cart_Checkout_OrderProductsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add');
    protected $_modelName = 'Vpc_Shop_Cart_OrderProducts';
    protected $_paging = 30;
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_editDialog = array(
            'controllerUrl' => Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('OrderProduct'),
            'width' => 400,
            'height' => 250
        );
        $this->_columns->add(new Vps_Grid_Column('product', trlVps('Product')))
            ->setData(new Vpc_Shop_Cart_Checkout_ProductData_ProductText());
        $this->_columns->add(new Vps_Grid_Column('amount', trlVps('Amount'), 50))
            ->setData(new Vpc_Shop_Cart_Checkout_ProductData_Amount());
        $this->_columns->add(new Vps_Grid_Column('info', trlVps('Info'), 150))
            ->setData(new Vpc_Shop_Cart_Checkout_ProductData_Info());

        $this->_columns->add(new Vps_Grid_Column('price', trlVps('Price'), 50))
            ->setData(new Vpc_Shop_Cart_Checkout_ProductData_Price())
            ->setRenderer('money');
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('shop_order_id', $this->_getParam('shop_order_id'));
        return $ret;
    }
}
