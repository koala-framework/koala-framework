<?php
class Kwc_Articles_Directory_ToTagModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_article_to_tag';

    protected $_referenceMap = array(
        'Article' => 'article_id->Kwc_Articles_Directory_Model',
        'Tag' => 'tag_id->Kwc_Articles_Directory_TagsModel',
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['tag_type'] = new Kwf_Model_Select_Expr_Parent('Tag', 'type');
        $this->_exprs['tag_name'] = new Kwf_Model_Select_Expr_Parent('Tag', 'name');
        $this->_exprs['tag_count_used'] = new Kwf_Model_Select_Expr_Parent('Tag', 'count_used');
        $this->_exprs['article_title'] = new Kwf_Model_Select_Expr_Parent('Article', 'title');
        $this->_exprs['article_visible'] = new Kwf_Model_Select_Expr_Parent('Article', 'visible');
    }
}
