<?php
class Vpc_Root_TrlRoot_Slave_Model extends Vps_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id'=>'root-en_2', 'name'=>'test', 'filename'=>'test', 'visible'=>1, 'custom_filename'=>0),
                array('component_id'=>'root-en_3', 'name'=>'test2 en', 'filename'=>'test2_en', 'visible'=>1, 'custom_filename'=>0)
            )
        );
        parent::__construct($config);
    }
}
