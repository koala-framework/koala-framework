<?php
class Kwc_Directories_CategorySimple_CategoriesToItemsModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_directory_categories_to_items';
    protected $_referenceMap = array(
        'Category' => 'category_id->Kwc_Directories_CategorySimple_CategoriesModel',
        'Item' => null,
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['category_name'] = new Kwf_Model_Select_Expr_Parent('Category', 'name');
        if (!count($this->_referenceMap['Item'])) {
            throw new Kwf_Exception("Reference 'Item' must be set in model '".get_class($this)."' and set in component as setting 'categoryToItemModelName'");
        }
    }
}
