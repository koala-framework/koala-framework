<?php
class Vpc_Cc_RootWithTrl_Master_Master_Category_PagesModel extends Vpc_Root_Category_GeneratorModel {

    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true,  'name'=>'Seite 1', 'filename' => '1', 'parent_id'=>'root-master-master-main', 'component'=>'empty', 'is_home'=>true,  'hide'=>false, 'custom_filename' => '0'),
                array('id'=>2, 'pos'=>2, 'visible'=>true,  'name'=>'Seite 2', 'filename' => '2', 'parent_id'=>'root-master-master-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => '0'),
                array('id'=>3, 'pos'=>3, 'visible'=>true, 'name'=>'Seite 3', 'filename' => '3', 'parent_id'=>'root-master-master-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => '0'),
                array('id'=>4, 'pos'=>1, 'visible'=>true,  'name'=>'Seite 4', 'filename' => '4', 'parent_id'=>'1',                       'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => '0'),
            )
        ));
        parent::__construct($config);
    }
}
