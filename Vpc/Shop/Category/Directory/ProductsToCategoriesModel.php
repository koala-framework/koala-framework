<?php
class Vpc_Shop_Category_Directory_ProductsToCategoriesModel
    extends Vpc_Directories_Category_Directory_ItemsToCategoriesModel
{
    protected $_name = 'vpc_shop_products_to_categories';

    protected function _setup()
    {
        $this->_referenceMap['Item'] = array(
            'columns'           => array('product_id'),
            'refTableClass'     => 'Vpc_Shop_Products',
            'refColumns'        => array('id')
        );
        parent::_setup();
    }
}
