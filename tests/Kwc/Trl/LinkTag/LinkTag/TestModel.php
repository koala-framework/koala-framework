<?php
class Kwc_Trl_LinkTag_LinkTag_TestModel extends Kwf_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-master_test1', 'component'=>'empty'),
                array('component_id'=>'root-master_test2', 'component'=>'extern'),
                array('component_id'=>'root-master_test3', 'component'=>'empty'),
            )
        );
        parent::__construct($config);
    }
}
