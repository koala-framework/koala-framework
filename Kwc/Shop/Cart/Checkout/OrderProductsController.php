<?php
class Kwc_Shop_Cart_Checkout_OrderProductsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add');
    protected $_modelName = 'Kwc_Shop_Cart_OrderProducts';
    protected $_paging = 30;
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_editDialog = array(
            'controllerUrl' => Kwc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('OrderProduct'),
            'width' => 400,
            'height' => 250
        );
        $this->_columns->add(new Kwf_Grid_Column('product', trlKwf('Product')))
            ->setData(new Kwc_Shop_Cart_Checkout_ProductData_ProductText());
        $this->_columns->add(new Kwf_Grid_Column('amount', trlKwf('Amount'), 50))
            ->setData(new Kwc_Shop_Cart_Checkout_ProductData_Amount());
        $this->_columns->add(new Kwf_Grid_Column('info', trlKwf('Info'), 150))
            ->setData(new Kwc_Shop_Cart_Checkout_ProductData_Info());

        $this->_columns->add(new Kwf_Grid_Column('price', trlKwf('Price'), 50))
            ->setData(new Kwc_Shop_Cart_Checkout_ProductData_Price())
            ->setRenderer('money');
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('shop_order_id', $this->_getParam('shop_order_id'));
        return $ret;
    }
}
