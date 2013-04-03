<?php
class Kwc_Trl_Table_MasterModel extends Kwf_Model_FnF
{
    protected $_referenceMap = array(
        'table' => array(
            'column' => 'component_id',
            'refModelClass' => 'Kwc_Trl_Table_OwnModel'
        )
    );

    public function __construct($config = array())
    {
        $config['columns'] = array('id', 'component_id', 'data');
        $config['namespace'] = 'table_trl_model';
        $config['primaryKey'] = 'id';
        $config['data'] = array(
            array('id'=>1, 'component_id'=>'root-master_table', 'data'=>'Daten aus Master'),
            array('id'=>2, 'component_id'=>'root-master_table', 'data'=>'Daten aus Master2')
        );
        parent::__construct($config);
    }
}
