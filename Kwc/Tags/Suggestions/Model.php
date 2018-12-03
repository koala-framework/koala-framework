<?php
class Kwc_Tags_Suggestions_Model extends Kwf_Model_Db
{
    protected $_table = 'kwc_tag_suggestions';

    protected $_referenceMap = array(
        'ComponentToTag' => 'tags_to_components_id->Kwc_Tags_ComponentToTag'
    );

    protected function _init()
    {
        parent::_init();
        $this->_referenceMap['User'] = 'user_id->' . Kwf_Registry::get('config')->user->model;
        $this->_exprs['component_id'] = new Kwf_Model_Select_Expr_Parent('ComponentToTag', 'component_id');
        $this->_exprs['tag_count_used'] = new Kwf_Model_Select_Expr_Parent('ComponentToTag', 'tag_count_used');
        $this->_exprs['tag_name'] = new Kwf_Model_Select_Expr_Parent('ComponentToTag', 'tag_name');
        $this->_exprs['user_email'] = new Kwf_Model_Select_Expr_Parent('User', 'email');
    }
}
