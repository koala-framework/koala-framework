<?php
abstract class Vpc_Directories_CategoryTree_Directory_Model extends Vps_Model_Tree
{
    protected $_rowClass = 'Vpc_Directories_CategoryTree_Directory_Row';

/*    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('parent_id');
        $this->_filters = array(
            'pos' => $filter,
            'filename' => 'Vps_Filter_Row_FilenameParents'
        );
    }*/
}