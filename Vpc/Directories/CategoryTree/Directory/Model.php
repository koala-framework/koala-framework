<?php
abstract class Vpc_Directories_CategoryTree_Directory_Model extends Vps_Db_Table_Abstract
{
    protected $_rowClass = 'Vpc_Directories_CategoryTree_Directory_Row';

    protected function _setup()
    {
        $this->_referenceMap['Parent'] = array(
            'columns' => 'parent_id',
            'refTableClass' => get_class($this),
            'refColumns' => 'id'
        );
        parent::_setup();
    }

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('parent_id');
/*        $this->_filters = array(
            'pos' => $filter,
            'filename' => 'Vps_Filter_Row_FilenameParents'
        );*/
    }
}