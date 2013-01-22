<?php
class Kwc_Articles_Directory_TagSuggestionsModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_article_tag_suggestions';

    // referenceModel Users has to be set in project
    protected $_referenceMap = array(
        'ArticleToTag' => 'article_to_tag_id->Kwc_Articles_Directory_ToTagModel',
        'User' => 'user_id->Users'
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['tag_count_used'] = new Kwf_Model_Select_Expr_Parent('ArticleToTag', 'tag_count_used');
        $this->_exprs['tag_name'] = new Kwf_Model_Select_Expr_Parent('ArticleToTag', 'tag_name');
        $this->_exprs['article_title'] = new Kwf_Model_Select_Expr_Parent('ArticleToTag', 'article_title');
        $this->_exprs['user_email'] = new Kwf_Model_Select_Expr_Parent('User', 'email');
    }
}
