<?php
class Vpc_Basic_Table_Trl_DataModel extends Vps_Model_Db
{
    protected $_table = 'vpc_basic_table_data_trl';

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        if (!isset($config['columnCount'])) throw new Vps_Exception('columnCount not given');
        $columns = array();
        for ($x = 1; $x <= $config['columnCount']; $x++) $columns[] = 'column' . $x;
        $this->_siblingModels[] = new Vps_Model_Field(array(
            'fieldName'=>'data',
            'columns' => $columns
        ));
    }
}
