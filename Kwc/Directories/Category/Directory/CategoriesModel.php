<?php
class Kwc_Directories_Category_Directory_CategoriesModel
    extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_directories_categories';
    protected $_toStringField = 'name';

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['pos'] = new Kwf_Filter_Row_Numberize();
        $this->_filters['pos']->setGroupBy('component_id');
    }
}
