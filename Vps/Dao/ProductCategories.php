<?php
class Vps_Dao_ProductCategories extends Zend_Db_Table
{
    protected $_name = 'product_categories';
    protected $_dependentTables = array('ProductProductsToCategories');
}
