<?p
class Vps_Dao_ProductProductsToCategories extends Zend_Db_Tab

    protected $_name = 'product_products_to_categories
    protected $_referenceMap    = arra
        'Product' => arra
            'columns'           => array('product_id'
            'refTableClass'     => 'Vps_Dao_ProductProducts
            'refColumns'        => array('id
        
        'Category' => arra
            'columns'           => array('category_id'
            'refTableClass'     => 'Vps_Dao_ProductCategories
            'refColumns'        => array('id
        )

