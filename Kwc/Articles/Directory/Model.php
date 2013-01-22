<?php
class Kwc_Articles_Directory_Model extends Kwf_Model_Db
{
    protected $_table = 'kwc_articles';
    protected $_toStringField = 'title';

    protected $_dependentModels = array(
        'Categories' => 'ArticleToCategory'
    );
     protected $_referenceMap = array(
         'Author' => 'author_id->Kwc_Articles_Directory_AuthorsModel',
     );

    protected function _init()
    {
        parent::_init();
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($authedUser) {
            $s = new Kwf_Model_Select();
            $s->whereEquals('user_id', $authedUser->id);
            if ($authedUser->role == 'external') {
                $this->_exprs['autheduser_visible'] = new Kwf_Model_Select_Expr_Not(new Kwf_Model_Select_Expr_Field('only_intern'));
            } else {
                $this->_exprs['autheduser_visible'] = new Kwf_Model_Select_Expr_Boolean(true);
            }
        } else {
            $this->_exprs['autheduser_visible'] = new Kwf_Model_Select_Expr_Boolean(false);
        }

        $this->_exprs['date_year'] = new Kwf_Model_Select_Expr_Date_Year('date');
        $this->_exprs['is_top_and_not_expired'] = new Kwf_Model_Select_Expr_And(array(
            new Kwf_Model_Select_Expr_Field('is_top'),
            new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Higher('expire_top_read_required', new Kwf_Date('now')),
                new Kwf_Model_Select_Expr_IsNull('expire_top_read_required'),
            ))
        ));
    }
}
