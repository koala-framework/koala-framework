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
        'ProductPrice' => array(
            'column'   => 'shop_product_price_id',
            'refModelClass' => 'Vpc_Shop_ProductPrices',
        )
    );

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['pos'] = new Vps_Filter_Row_Numberize();
        $this->_filters['pos']->setGroupBy('shop_order_id');
    }

    protected function _init()
    {
        parent::_init();
        $this->_siblingModels[] = new Vps_Model_Field(array(
            'fieldName' => 'data'
        ));
    }

    
    public function hasColumn($col)
    {
        if ($col == 'visible') return false;
        if ($col == 'component_id') return false;
        return parent::hasColumn($col);
    }
}
