<?php
class Kwc_Articles_Directory_CategoriesModel extends Kwf_Model_Tree
{
    protected $_table = 'kwc_article_categories';
    protected $_toStringField = 'name';

    protected $_dependentModels = array(
        'Categories' => 'Kwc_Articles_Directory_ToCategoryModel'
    );

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['pos'] = new Kwf_Filter_Row_Numberize();
        $this->_filters['pos']->setGroupBy('parent_id');
    }

    protected function _init()
    {
        parent::_init();
        $this->_exprs['count_used'] = new Kwf_Model_Select_Expr_Child_Count('Categories');
    }
}
