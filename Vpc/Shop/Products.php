<?php
class Vpc_Shop_Products extends Vps_Model_Db
{
    protected $_rowClass = 'Vpc_Shop_Product';
    protected $_table = 'vpc_shop_products';
    protected $_filters = array('pos');
    protected $_dependentModels = array('OrderProducts'=>'Vpc_Shop_Cart_OrderProducts');
}
