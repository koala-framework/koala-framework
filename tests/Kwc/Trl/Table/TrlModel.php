<?php
class Kwc_Trl_Table_TrlModel extends Kwf_Model_FnF
{
    public function __construct($config = array())
    {
        $config['columns'] = array('id', 'component_id', 'data', 'master_id');
        $config['namespace'] = 'table_trl_model';
        $config['primaryKey'] = 'id';
        $config['data'] = array(
            array('id'=>1, 'component_id'=>'root-en', 'data'=>'Daten aus Trl', 'master_id'=>2),
        );
        parent::__construct($config);
    }
}
