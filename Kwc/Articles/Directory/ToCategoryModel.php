<?php
class Kwc_Articles_Directory_ToCategoryModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_article_to_category';

    protected $_referenceMap = array(
        'Article' => 'article_id->Kwc_Articles_Directory_Model',
        'Category' => 'category_id->Kwc_Articles_Directory_CategoriesModel',
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['category_type'] = new Kwf_Model_Select_Expr_Parent('Category', 'type');
        $this->_exprs['category_name'] = new Kwf_Model_Select_Expr_Parent('Category', 'name');
        $this->_exprs['category_count_used'] = new Kwf_Model_Select_Expr_Parent('Category', 'count_used');
        $this->_exprs['article_title'] = new Kwf_Model_Select_Expr_Parent('Article', 'title');
        $this->_exprs['article_visible'] = new Kwf_Model_Select_Expr_Parent('Article', 'visible');
    }
}
