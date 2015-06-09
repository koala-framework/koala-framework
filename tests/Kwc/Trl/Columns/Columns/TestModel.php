<?php
class Kwc_Trl_Columns_Columns_TestModel extends Kwf_Model_FnF
{
    public function __construct(array $config = array())
    {
        $config = array(
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id' => 'root-master_test', 'type'=>'2col-50_50')
            )
        );
        parent::__construct($config);
    }
}
