<?php
class Kwc_Root_Category_Trl_GeneratorModel extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwf_pages_trl';
    protected $_toStringField = 'name';
    protected $_rowClass = 'Kwc_Root_Category_Trl_GeneratorRow';

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['filename'] = new Kwc_Root_Category_Trl_FilenameFilter();
    }
}
