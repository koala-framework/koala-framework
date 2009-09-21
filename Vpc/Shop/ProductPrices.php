<?php
class Vpc_Shop_ProductPrices extends Vps_Model_Db
{
    protected $_table = 'vpc_shop_product_prices';
    protected $_referenceMap = array(
        'Product' => array(
            'column'   => 'shop_product_id',
            'refModelClass' => 'Vpc_Shop_Products',
        )
    );
    protected $_dependentModels = array(
        'OrderProducts' => 'Vpc_Shop_Cart_OrderProducts',
    );
}
