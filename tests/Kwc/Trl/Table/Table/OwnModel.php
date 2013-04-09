<?php
class Kwc_Trl_Table_Table_OwnModel extends Kwf_Model_FnF
{
    protected $_dependentModels = array(
        'tableData' => 'Kwc_Trl_Table_Table_MasterModel'
    );

    protected function _init()
    {
        parent::_init();
    }

    public function __construct($config = array())
    {
        $config['columns'] = array();
        $config['primaryKey'] = 'component_id';
        $config['data'] = array(
            array('component_id'=>'root-master_table', 'table_style'=>'standard'),
        );
        parent::__construct($config);
    }
}
