<?php
class Vpc_Shop_Cart_OrderProducts extends Vps_Model_Db
{
    protected $_rowClass = 'Vpc_Shop_Cart_OrderProduct';
    protected $_table = 'vpc_shop_order_products';
    protected $_referenceMap = array(
        'Order' => array(
            'column'   => 'shop_order_id',
            'refModelClass' => 'Vpc_Shop_Cart_Orders'
        ),
        'Product' => array(
            'column'   => 'shop_product_id',
            'refModelClass' => 'Vpc_Shop_Products',
        )
    );
    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['pos'] = new Vps_Filter_Row_Numberize();
        $this->_filters['pos']->setGroupBy('shop_order_id');
    }

}
