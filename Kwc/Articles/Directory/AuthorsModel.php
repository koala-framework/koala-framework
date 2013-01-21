<?php
class Kwc_Articles_Directory_AuthorsModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_article_authors';
    protected $_toStringField = 'name';

    protected $_dependentModels = array(
        'Articles' => 'Kwc_Articles_Directory_Model',
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['name'] = new Kwf_Model_Select_Expr_Concat(array(
            new Kwf_Model_Select_Expr_Field('firstname'),
            new Kwf_Model_Select_Expr_String(' '),
            new Kwf_Model_Select_Expr_Field('lastname')
        ));
    }
}
