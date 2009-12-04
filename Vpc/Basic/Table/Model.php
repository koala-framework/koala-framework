<?php
class Vpc_Basic_Table_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_basic_table';
    protected $_dependentModels = array('tableData' => 'Vpc_Basic_Table_ModelData');

    protected function _init()
    {
        $this->_siblingModels[] = new Vps_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }
}
