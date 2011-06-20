<?php
class Vpc_Basic_Table_Trl_DataModel extends Vps_Model_Db
{
    protected $_table = 'vpc_basic_table_data_trl';
    protected $_rowClass = 'Vpc_Basic_Table_Trl_RowData';

    protected function _init()
    {
        $this->_siblingModels[] = new Vps_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }
}
