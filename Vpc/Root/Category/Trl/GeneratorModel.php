<?php
class Vpc_Root_Category_Trl_GeneratorModel extends Vps_Model_Db_Proxy
{
    protected $_table = 'vps_pages_trl';
    protected $_toStringField = 'name';
    protected $_rowClass = 'Vpc_Root_Category_Trl_GeneratorRow';

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['filename'] = new Vpc_Root_Category_Trl_FilenameFilter();
    }
}
