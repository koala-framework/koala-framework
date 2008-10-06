<?php
class Vpc_Shop_Products extends Vps_Db_Table_Abstract
{
    protected $_rowClass = 'Vpc_Shop_Product';

    protected $_name = 'vpc_shop_products';
    protected $_filters = array('filename');
}
