<?php
class Kwc_Trl_Table_OwnModel extends Kwf_Model_FnF
{
    protected $_dependentModels = array(
        'tableData' => 'Kwc_Trl_Table_MasterModel'
    );

    public function __construct($config = array())
    {
        $config['columns'] = array('component_id', 'data');
        $config['namespace'] = 'trl_table_model';
        $config['primaryKey'] = 'component_id';
        $config['data'] = array(
            array('component_id'=>'root-master_table', 'data'=>'{"table_style":"standard"}'),
        );
        parent::__construct($config);
    }
}
