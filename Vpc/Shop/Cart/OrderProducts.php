<?php
class Vpc_Shop_Cart_OrderProducts extends Vps_Db_Table_Abstract
{
    protected $_name = 'vpc_shop_order_products';
    protected $_filters = array('pos');
    protected $_referenceMap = array(
        'Order' => array(
            'columns'   => array('shop_order_id'),
            'refTableClass' => 'Vpc_Shop_Cart_Orders',
            'refColumns'    => 'id'
        ),
        'Product' => array(
            'columns'   => array('shop_product_id'),
            'refTableClass' => 'Vpc_Shop_Products',
            'refColumns'    => 'id'
        )
    );
    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['pos'] = new Vps_Filter_Row_Numberize();
        $this->_filters['pos']->setGroupBy('shop_order_id');
    }

}
