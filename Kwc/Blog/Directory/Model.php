<?php
class Kwc_Blog_Directory_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_blog_posts';
    protected $_rowClass = 'Kwc_Blog_Directory_Row';
    protected $_dependentModels = array(
        'Categories' => 'Kwc_Blog_Category_Directory_BlogPostsToCategoriesModel'
    );

    protected function _init()
    {
        parent::_init();
        $this->_referenceMap = array(
            'Author' => array(
                'refModel' => Kwf_Registry::get('userModel'),
                'column' => 'author_id',
            ),
        );
        $this->_exprs['author_firstname'] = new Kwf_Model_Select_Expr_Parent('Author', 'firstname');
        $this->_exprs['author_lastname'] = new Kwf_Model_Select_Expr_Parent('Author', 'lastname');
    }
}
