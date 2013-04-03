<?php
class Kwc_Basic_Table_Trl_DataModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_basic_table_data_trl';
    protected $_rowClass = 'Kwc_Basic_Table_Trl_RowData';

    protected function _init()
    {
        $this->_siblingModels[] = new Kwf_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }

    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        return parent::getRows($where, $order, $limit, $start);
    }
}
