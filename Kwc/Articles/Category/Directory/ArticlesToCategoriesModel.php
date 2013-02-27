<?php
class Kwc_Articles_Category_Directory_ArticlesToCategoriesModel extends Kwc_Directories_Category_Directory_ItemsToCategoriesModel
{
    protected $_table = 'kwc_article_to_category';

    protected function _init()
    {
        $this->_referenceMap['Item'] = array(
            'column'           => 'article_id',
            'refModelClass'     => 'Kwc_Articles_Directory_Model'
        );
        parent::_init();
        $this->_exprs['category_type'] = new Kwf_Model_Select_Expr_Parent('Item', 'type');
        $this->_exprs['category_name'] = new Kwf_Model_Select_Expr_Parent('Item', 'name');
        $this->_exprs['category_count_used'] = new Kwf_Model_Select_Expr_Parent('Item', 'count_used');
    }
}
