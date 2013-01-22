<?php
class Kwc_Articles_Directory_TagsModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_article_tags';
    protected $_toStringField = 'name';

    protected $_dependentModels = array(
        'ArticleToTag' => 'Kwc_Articles_Directory_ToTagModel'
    );

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['pos'] = new Kwf_Filter_Row_Numberize();
        $this->_filters['pos']->setGroupBy('type');
    }

    protected function _init()
    {
        parent::_init();
        $this->_exprs['count_used'] = new Kwf_Model_Select_Expr_Child_Count('ArticleToTag');
        $s = new Kwf_Model_Select();
        $s->whereEquals('article_visible', true);
        $s->whereEquals('article_autheduser_visible', true);
        $this->_exprs['count_article_autheduser_visible'] = new Kwf_Model_Select_Expr_Child_Count('ArticleToTag', $s);
    }
}
