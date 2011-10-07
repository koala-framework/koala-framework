<?php
abstract class Kwc_Directories_CategoryTree_Directory_Model extends Kwf_Model_Tree
{
    protected $_rowClass = 'Kwc_Directories_CategoryTree_Directory_Row';

/*    protected function _setupFilters()
    {
        $filter = new Kwf_Filter_Row_Numberize();
        $filter->setGroupBy('parent_id');
        $this->_filters = array(
            'pos' => $filter,
            'filename' => 'Kwf_Filter_Row_FilenameParents'
        );
    }*/
}