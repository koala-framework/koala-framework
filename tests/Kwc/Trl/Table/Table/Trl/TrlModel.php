<?php
class Kwc_Trl_Table_Table_Trl_TrlModel extends Kwf_Model_FnF
{
    protected function _init()
    {
        parent::_init();
    }

    public function __construct($config = array())
    {
        $config['columns'] = array();
        $config['namespace'] = 'table_trl_model';
        $config['primaryKey'] = 'id';
        $config['data'] = array(
            array('id'=>2, 'visible'=>1, 'component_id'=>'root-en_table', 'master_id'=>1 , 'data'=>'[]'),
            array('id'=>1, 'visible'=>1, 'component_id'=>'root-en_table', 'column1'=>'Abc', 'column3'=>'234', 'master_id'=>2),
        );
        parent::__construct($config);
    }
}
