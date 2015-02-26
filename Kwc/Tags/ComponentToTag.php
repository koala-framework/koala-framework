<?php
class Kwc_Tags_ComponentToTag extends Kwf_Model_Db
{
    protected $_table = 'kwc_tags_to_components';

    protected $_referenceMap = array(
        'Tag' => 'tag_id->Kwc_Tags_Model',
        'Component' => 'component_id->Kwc_Tags_ComponentModel'
    );
    protected $_dependentModels = array(
        'Suggestions' => 'Kwc_Tags_Suggestions_Model'
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['tag_name'] = new Kwf_Model_Select_Expr_Parent('Tag', 'name');
        $this->_exprs['tag_count_used'] = new Kwf_Model_Select_Expr_Parent('Tag', 'count_used');
    }
}
