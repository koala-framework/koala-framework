<?php
class Kwc_Trl_LinkTag_LinkTag_TestModel extends Kwc_Basic_LinkTag_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-master_test1', 'component'=>'empty', 'data'=>null),
                array('component_id'=>'root-master_test2', 'component'=>'extern', 'data'=>null),
                array('component_id'=>'root-master_test3', 'component'=>'empty', 'data'=>null),
            )
        ));
        parent::__construct($config);
    }
}
