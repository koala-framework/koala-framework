<?php
class Kwc_Articles_Directory_ViewsModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_article_views';

    // referenceModel Users has to be set in project
    protected $_referenceMap = array(
        'Acticle' => 'article_id->Kwc_Articles_Directory_Model',
        'User' => 'user_id->Users',
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['article_is_top'] = new Kwf_Model_Select_Expr_Parent('Acticle', 'is_top');
        $this->_exprs['article_visible'] = new Kwf_Model_Select_Expr_Parent('Acticle', 'visible');
        $this->_exprs['article_autheduser_visible'] = new Kwf_Model_Select_Expr_Parent('Acticle', 'autheduser_visible');
    }
}
