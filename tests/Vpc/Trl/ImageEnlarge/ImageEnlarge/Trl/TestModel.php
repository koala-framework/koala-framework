<?php
class Vpc_Trl_ImageEnlarge_ImageEnlarge_Trl_TestModel extends Vps_Model_Proxy
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-en_test1', 'own_image'=>0),
                array('component_id'=>'root-en_test2', 'own_image'=>0),
                array('component_id'=>'root-en_test3', 'own_image'=>1),
                array('component_id'=>'root-en_test4', 'own_image'=>1),
                array('component_id'=>'root-en_test5', 'own_image'=>1),
                array('component_id'=>'root-en_test6', 'own_image'=>0),
            )
        ));
        parent::__construct($config);
    }
}
