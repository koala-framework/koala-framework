<?php
class Vpc_Shop_Category_Directory_ProductsToCategoriesModel
    extends Vpc_Directories_Category_Directory_ItemsToCategoriesModel
{
    protected $_table = 'vpc_shop_products_to_categories';

    protected function _init()
    {
        $this->_referenceMap['Item'] = array(
            'column'        => 'product_id',
            'refModelClass' => 'Vpc_Shop_Products',
        );
        parent::_init();
    }
}
