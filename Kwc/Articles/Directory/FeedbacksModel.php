<?php
class Kwc_Articles_Directory_FeedbacksModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_article_feedbacks';

    // referenceModel Users has to be set in project
    protected $_referenceMap = array(
        'Acticle' => 'article_id->Kwc_Articles_Directory_Model',
        'User' => 'user_id->Users'
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['user_email'] = new Kwf_Model_Select_Expr_Parent('User', 'email');
    }
}
