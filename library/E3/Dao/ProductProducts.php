<?php
class E3_Dao_ProductProducts extends Zend_Db_Table
{
    protected $_name = 'product_products';
    protected $_dependentTables = array('E3_Dao_ProductProductsToCategories');
}
