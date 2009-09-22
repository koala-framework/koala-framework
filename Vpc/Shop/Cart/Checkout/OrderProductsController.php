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
            'height' => 200
        );
        $this->_columns->add(new Vps_Grid_Column('product', trlVps('Product')))
            ->setData(new Vps_Data_Table_Parent(array('ProductPrice', 'Product')));
        $this->_columns->add(new Vps_Grid_Column('price', trlVps('Price'), 50))
            ->setRenderer('money');
        $this->_columns->add(new Vps_Grid_Column('amount', trlVps('Amount'), 50));

        //keine optimale lösung, size gibts nur beim babytuch und da auch nicht zwingend immer
        $this->_columns->add(new Vps_Grid_Column('size', trlVps('Size'), 100))
            ->setType('int');
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('shop_order_id', $this->_getParam('shop_order_id'));
        return $ret;
    }
}
