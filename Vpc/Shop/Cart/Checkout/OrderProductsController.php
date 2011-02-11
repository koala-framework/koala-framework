<?php
class Vpc_Shop_Cart_Checkout_OrderProductsController_ProductText extends Vps_Data_Abstract
{
    public function load($row)
    {
        $data = Vpc_Shop_AddToCartAbstract_OrderProductData::getInstance($row->add_component_class);
        return $data->getProductText($row);
    }
}
class Vpc_Shop_Cart_Checkout_OrderProductsController_Price extends Vps_Data_Abstract
{
    public function load($row)
    {
        $data = Vpc_Shop_AddToCartAbstract_OrderProductData::getInstance($row->add_component_class);
        return $data->getPrice($row);
    }
}
class Vpc_Shop_Cart_Checkout_OrderProductsController_Amount extends Vps_Data_Abstract
{
    public function load($row)
    {
        $data = Vpc_Shop_AddToCartAbstract_OrderProductData::getInstance($row->add_component_class);
        return $data->getAmount($row);
    }
}
class Vpc_Shop_Cart_Checkout_OrderProductsController_Info extends Vps_Data_Abstract
{
    public function load($row)
    {
        $data = Vpc_Shop_AddToCartAbstract_OrderProductData::getInstance($row->add_component_class);
        $parts = array();
        foreach ($data->getAdditionalOrderData($row) as $data) {
            if ($data['class'] == 'amount') continue;
            $parts[] = $data['name'] . ': ' . $data['value'];
        }
        return implode(', ', $parts);
    }
}
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
            ->setData(new Vpc_Shop_Cart_Checkout_OrderProductsController_ProductText());
        $this->_columns->add(new Vps_Grid_Column('amount', trlVps('Amount'), 50))
            ->setData(new Vpc_Shop_Cart_Checkout_OrderProductsController_Amount());
        $this->_columns->add(new Vps_Grid_Column('info', trlVps('Info'), 150))
            ->setData(new Vpc_Shop_Cart_Checkout_OrderProductsController_Info());

        $this->_columns->add(new Vps_Grid_Column('price', trlVps('Price'), 50))
            ->setData(new Vpc_Shop_Cart_Checkout_OrderProductsController_Price())
            ->setRenderer('money');
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('shop_order_id', $this->_getParam('shop_order_id'));
        return $ret;
    }
}
