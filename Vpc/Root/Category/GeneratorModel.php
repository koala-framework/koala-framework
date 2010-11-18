<?php
class Vpc_Root_Category_GeneratorModel extends Vps_Model_Tree
{
    protected $_table = 'vps_pages';
    protected $_toStringField = 'name';
    protected $_rowClass = 'Vpc_Root_Category_GeneratorRow';

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['filename'] = new Vpc_Root_Category_FilenameFilter();
        $this->_filters['pos'] = new Vps_Filter_Row_Numberize();
        $this->_filters['pos']->setGroupBy('parent_id');
    }

    public function getRootNodes($select = array())
    {
        throw new Vps_Exception_NotYetImplemented();
    }
}
