<?php
class Kwc_Basic_Table_ModelData extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_basic_table_data';
    protected $_rowClass = 'Kwc_Basic_Table_RowData';
    protected $_referenceMap = array(
        'table' => array(
            'column' => 'component_id',
            'refModelClass' => 'Kwc_Basic_Table_Model'
        )
    );

    protected function _init()
    {
        $this->_siblingModels[] = new Kwf_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }

    protected function _setupFilters()
    {
        $filter = new Kwf_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
