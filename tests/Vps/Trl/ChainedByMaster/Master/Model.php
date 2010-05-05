<?php
class Vps_Trl_ChainedByMaster_Master_Model extends Vpc_Root_Category_GeneratorModel {

    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Seite 1', 'filename' => 's1', 'parent_id'=>'root-master', 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'visible'=>1, 'custom_filename' => 0, 'tags' => ''),
                array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Seite 2', 'filename' => 's2', 'parent_id'=>'1', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'visible'=>1, 'custom_filename' => 0, 'tags' => ''),
            )
        ));
        parent::__construct($config);
    }
}
