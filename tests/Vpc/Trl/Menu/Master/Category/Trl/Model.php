<?php
class Vpc_Trl_Menu_Master_Category_Trl_Model extends Vpc_Root_Category_Trl_GeneratorModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('component_id'=>'root-en-main_1', 'name' => 'Page 1', 'filename' => 'p1', 'visible' => 1, 'custom_filename' => 0),
                array('component_id'=>'root-en-main_2', 'name' => 'Page 2', 'filename' => 'p2', 'visible' => 1, 'custom_filename' => 0),
                array('component_id'=>'root-en-main_3', 'name' => 'Page 3', 'filename' => 'p3', 'visible' => 1, 'custom_filename' => 0),
                array('component_id'=>'root-en-main_4', 'name' => 'Page 4', 'filename' => 'p4', 'visible' => 1, 'custom_filename' => 0),
                array('component_id'=>'root-en-main_5', 'name' => 'Page 5', 'filename' => 'p5', 'visible' => 1, 'custom_filename' => 0),
            ),
            'primaryKey' => 'component_id'
        ));
        parent::__construct($config);
    }
}
