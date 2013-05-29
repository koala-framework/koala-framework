<?php
class Kwc_Cc_RootWithTrl_Master_Master_Category_PagesModel extends Kwc_Root_Category_GeneratorModel {

    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true,  'name'=>'Seite 1', 'filename' => '1', 'parent_id'=>'root-master-master-main', 'component'=>'empty', 'is_home'=>true,  'hide'=>false, 'custom_filename' => '0', 'parent_subroot_id' => 'root-master-master'),
                array('id'=>2, 'pos'=>2, 'visible'=>true,  'name'=>'Seite 2', 'filename' => '2', 'parent_id'=>'root-master-master-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => '0', 'parent_subroot_id' => 'root-master-master'),
                array('id'=>3, 'pos'=>3, 'visible'=>true, 'name'=>'Seite 3', 'filename' => '3', 'parent_id'=>'root-master-master-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => '0', 'parent_subroot_id' => 'root-master-master'),
                array('id'=>4, 'pos'=>1, 'visible'=>true,  'name'=>'Seite 4', 'filename' => '4', 'parent_id'=>'1',                       'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => '0', 'parent_subroot_id' => 'root-master-master'),
            )
        ));
        parent::__construct($config);
    }
}
