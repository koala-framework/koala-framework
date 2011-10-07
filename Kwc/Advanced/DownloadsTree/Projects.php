<?php
class Kwc_Advanced_DownloadsTree_Projects extends Kwf_Model_Tree
{
    protected $_table = 'kwc_downloadstree_projects';
    protected $_dependentModels = array(
        'Downloads' => 'Kwc_Advanced_DownloadsTree_Downloads',
    );
    protected function _setupFilters()
    {
        parent::_setupFilters();
        $filter = new Kwf_Filter_Row_Numberize();
        $filter->setGroupBy('parent_id');
        $this->_filters['pos'] = $filter;
    }
}
