<?php
class Kwc_Basic_Table_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_basic_table';
    protected $_dependentModels = array('tableData' => 'Kwc_Basic_Table_ModelData');
    protected $_rowClass = 'Kwc_Basic_Table_Row';

    protected function _init()
    {
        $this->_siblingModels[] = new Kwf_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }
}
