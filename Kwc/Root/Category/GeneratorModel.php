<?php
class Kwc_Root_Category_GeneratorModel extends Kwf_Model_Tree
{
    protected $_table = 'kwf_pages';
    protected $_toStringField = 'name';
    protected $_rowClass = 'Kwc_Root_Category_GeneratorRow';

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['filename'] = new Kwc_Root_Category_FilenameFilter();
        $this->_filters['pos'] = new Kwf_Filter_Row_Numberize();
        $this->_filters['pos']->setGroupBy('parent_id');
    }

    public function getRootNodes($select = array())
    {
        throw new Kwf_Exception_NotYetImplemented();
    }
}
