<?php
class Vpc_Columns_ColumnsModel extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_columns';
    protected $_referenceMap = array(
        'Component' => array(
            'refModelClass' => 'Vpc_Columns_Model',
            'column' => 'component_id'
        )
    );

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
