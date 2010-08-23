<?php
class Vpc_Cc_RootWithTrl_Master_Master_Category_Trl_Model extends Vpc_Root_Category_Trl_GeneratorModel {

    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('component_id'=>'root-master-slave-main_2', 'name' => '2_trl', 'filename' => '2', 'visible' => '0', 'custom_filename' => '0'),
                array('component_id'=>'root-master-slave-main_3', 'name' => '3_trl', 'filename' => '3_trl', 'visible' => '1', 'custom_filename' => '0')
            ),
            'primaryKey' => 'component_id'
        ));
        parent::__construct($config);
    }
}
