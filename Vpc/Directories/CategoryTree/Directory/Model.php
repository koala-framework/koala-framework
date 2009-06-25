<?php
abstract class Vpc_Directories_CategoryTree_Directory_Model extends Vps_Model_Db
{
    protected $_rowClass = 'Vpc_Directories_CategoryTree_Directory_Row';

    protected $_dependentModels = array('Child' => '');
    protected $_referenceMap = array(
        'Parent' => array(
            'column' => 'parent_id',
            'refModelClass' => ''
        )
    );

    protected function _init()
    {
        $this->_dependentModels['Child'] = get_class($this);
        $this->_referenceMap['Parent']['refModelClass'] = get_class($this);
        parent::_init();
    }

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