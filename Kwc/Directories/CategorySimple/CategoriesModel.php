<?php
class Kwc_Directories_CategorySimple_CategoriesModel extends Kwf_Model_Tree
{
    protected $_table = 'kwc_directory_categories';
    protected $_toStringField = 'name';

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['pos'] = new Kwf_Filter_Row_Numberize();
        $this->_filters['pos']->setGroupBy(array('parent_id', 'component_id'));
    }

    protected function _init()
    {
        parent::_init();
    }
}
