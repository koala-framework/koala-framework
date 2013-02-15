<?php
class Kwc_Tags_Model extends Kwf_Model_Db
{
    protected $_table = 'kwc_tags';
    protected $_toStringField = 'name';

    protected $_dependentModels = array(
        'ComponentToTag' => 'Kwc_Tags_ComponentToTag'
    );

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['pos'] = new Kwf_Filter_Row_Numberize();
    }

    protected function _init()
    {
        parent::_init();
        $this->_exprs['count_used'] = new Kwf_Model_Select_Expr_Child_Count('ComponentToTag');
    }
}
