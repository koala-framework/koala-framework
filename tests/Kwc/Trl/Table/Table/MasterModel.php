<?php
class Kwc_Trl_Table_Table_MasterModel extends Kwf_Model_FnF
{
    protected $_referenceMap = array(
        'table' => array(
            'column' => 'component_id',
            'refModelClass' => 'Kwc_Trl_Table_Table_OwnModel'
        )
    );

    protected function _init()
    {
        parent::_init();
    }

    public function __construct($config = array())
    {
        $config['columns'] = array();
        $config['primaryKey'] = 'id';
        $config['data'] = array(
            array('id'=>1, 'component_id'=>'root-master_table', 'pos'=>0, 'visible'=>1, 'column1'=>'Abc', 'column2'=>'1234', 'column3'=>'234'),
            array('id'=>2, 'component_id'=>'root-master_table', 'pos'=>1, 'visible'=>1, 'column1'=>'Cde', 'column2'=>'4321', 'column3'=>'1234')
        );
        parent::__construct($config);
    }
}
