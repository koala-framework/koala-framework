<?php
class Vps_Component_PagesModel extends Vps_Model_Tree
{
    protected $_table = 'vps_pages';
    protected $_toStringField = 'name';
    protected $_rowClass = 'Vps_Component_Pages_Row';

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['filename'] = new Vps_Component_Pages_FilenameFilter();
        $this->_filters['pos'] = new Vps_Filter_Row_Numberize();
        $this->_filters['pos']->setGroupBy('parent_id');
    }

    public function getRootNodes($select = array())
    {
        throw new Vps_Exception_NotYetImplemented();
    }
}
