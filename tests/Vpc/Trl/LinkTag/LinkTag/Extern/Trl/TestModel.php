<?php
class Vpc_Trl_LinkTag_LinkTag_Extern_Trl_TestModel extends Vps_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-en_test2-link', 'target'=>'http://www.vivid-planet.com/en'),
            )
        );
        parent::__construct($config);
    }
}
