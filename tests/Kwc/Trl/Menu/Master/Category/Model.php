<?php
class Kwc_Trl_Menu_Master_Category_Model extends Kwc_Root_Category_GeneratorModel {

    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Seite 1', 'filename' => 's1', 'parent_id'=>'root-master-main', 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'visible'=>1, 'custom_filename' => 0, 'parent_subroot_id' => 'root-master'),
                array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Seite 2', 'filename' => 's2', 'parent_id'=>'root-master-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'visible'=>1, 'custom_filename' => 0, 'parent_subroot_id' => 'root-master'),
                array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Seite 3', 'filename' => 's3', 'parent_id'=>'1',    'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'visible'=>1, 'custom_filename' => 0, 'parent_subroot_id' => 'root-master'),
                array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'Seite 4', 'filename' => 's4', 'parent_id'=>'3',    'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'visible'=>1, 'custom_filename' => 0, 'parent_subroot_id' => 'root-master'),
                array('id'=>5, 'pos'=>1, 'visible'=>true, 'name'=>'Seite 5', 'filename' => 's5', 'parent_id'=>'4',    'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'visible'=>1, 'custom_filename' => 0, 'parent_subroot_id' => 'root-master')
            )
        ));
        parent::__construct($config);
    }
}
