<?php
class Vpc_Basic_Table_ModelData extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_basic_table_data';
    protected $_rowClass = 'Vpc_Basic_Table_RowData';
    protected $_referenceMap = array(
        'table' => array(
            'column' => 'component_id',
            'refModelClass' => 'Vpc_Basic_Table_Model'
        )
    );

    protected function _init()
    {
        $this->_siblingModels[] = new Vps_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
