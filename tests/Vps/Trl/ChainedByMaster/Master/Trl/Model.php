<?php
class Vps_Trl_ChainedByMaster_Master_Trl_Model extends Vpc_Root_Category_Trl_GeneratorModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('component_id'=>'root-en_1', 'name' => 'Page 1', 'filename' => 'p1', 'visible' => 1, 'custom_filename' => 0),
                array('component_id'=>'root-en_2', 'name' => 'Page 2', 'filename' => 'p2', 'visible' => 1, 'custom_filename' => 0),
            ),
            'primaryKey' => 'component_id'
        ));
        parent::__construct($config);
    }
}
