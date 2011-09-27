<?php
class Vpc_Trl_LinkTag_LinkTag_Extern_TestModel extends Vps_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'primaryKey' => 'component_id',
            'columns' => array(),
            'data'=> array(
                array('component_id'=>'root-master_test2-child', 'target'=>'http://www.vivid-planet.com/', 'open_type'=>'self'),
            )
        );
        parent::__construct($config);
    }
}
