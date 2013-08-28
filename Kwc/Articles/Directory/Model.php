<?php
class Kwc_Articles_Directory_Model extends Kwf_Model_Db
{
    protected $_table = 'kwc_articles';
    protected $_toStringField = 'title';
    protected $_rowClass = 'Kwc_Articles_Directory_Row';

    protected $_dependentModels = array(
        'Views' => 'Kwc_Articles_Directory_ViewsModel'
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
            if ($authedUser->__get('role') == 'external') { // $authedUser->role throws error when role is an expression (PHP Bug?)
                $this->_exprs['autheduser_visible'] = new Kwf_Model_Select_Expr_Not(new Kwf_Model_Select_Expr_Field('only_intern'));
            } else {
                $this->_exprs['autheduser_visible'] = new Kwf_Model_Select_Expr_Boolean(true);
            }
            $this->_exprs['autheduser_read'] = new Kwf_Model_Select_Expr_Child_Contains('Views', $s);
        } else {
            $this->_exprs['autheduser_visible'] = new Kwf_Model_Select_Expr_Boolean(false);
        }

        $this->_exprs['date_year'] = new Kwf_Model_Select_Expr_Date_Year('date');
        $this->_exprs['is_top'] = new Kwf_Model_Select_Expr_If(
            new Kwf_Model_Select_Expr_And(array(
                new Kwf_Model_Select_Expr_Equal('is_top_checked', 1),
                new Kwf_Model_Select_Expr_Or(array(
                    new Kwf_Model_Select_Expr_IsNull('is_top_expire'),
                    new Kwf_Model_Select_Expr_HigherEqual('is_top_expire', new Kwf_Date(mktime())),
                )),
            )),
            new Kwf_Model_Select_Expr_String(1),
            new Kwf_Model_Select_Expr_String(0)
        );
    }
}
