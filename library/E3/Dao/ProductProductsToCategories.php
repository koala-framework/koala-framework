<?php
class E3_Dao_ProductProductsToCategories extends Zend_Db_Table
{
    protected $_name = 'product_products_to_categories';
    protected $_referenceMap    = array(
        'Product' => array(
            'columns'           => array('product_id'),
            'refTableClass'     => 'E3_Dao_ProductProducts',
            'refColumns'        => array('id')
        ),
        'Category' => array(
            'columns'           => array('category_id'),
            'refTableClass'     => 'E3_Dao_ProductCategories',
            'refColumns'        => array('id')
        ));
}
