<?php
class Vpc_Advanced_DownloadsTree_Projects extends Vps_Model_Tree
{
    protected $_table = 'vpc_downloadstree_projects';
    protected $_dependentModels = array(
        'Downloads' => 'Vpc_Advanced_DownloadsTree_Downloads',
    );
    protected function _setupFilters()
    {
        parent::_setupFilters();
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('parent_id');
        $this->_filters['pos'] = $filter;
    }
}
