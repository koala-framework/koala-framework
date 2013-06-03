<?php
class Kwc_Cc_PageTree_Master_Category_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Seite 1', 'filename' => '1', 'parent_id'=>'root-master-main',   'component'=>'none', 'is_home'=>true,  'hide'=>false, 'custom_filename' => false, 'parent_subroot_id'=>'root-master'),
                array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Seite 2', 'filename' => '2', 'parent_id'=>'root-master-main',   'component'=>'none', 'is_home'=>false, 'hide'=>false, 'custom_filename' => false, 'parent_subroot_id'=>'root-master'),
                array('id'=>3, 'pos'=>3, 'visible'=>true, 'name'=>'Seite 3', 'filename' => '3', 'parent_id'=>'root-master-main',   'component'=>'none', 'is_home'=>false, 'hide'=>false, 'custom_filename' => false, 'parent_subroot_id'=>'root-master'),
                array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'Seite 4', 'filename' => '4', 'parent_id'=>'1',                  'component'=>'none', 'is_home'=>false, 'hide'=>false, 'custom_filename' => false, 'parent_subroot_id'=>'root-master'),
                array('id'=>5, 'pos'=>1, 'visible'=>true, 'name'=>'Seite 5', 'filename' => '5', 'parent_id'=>'root-master-bottom', 'component'=>'none', 'is_home'=>false, 'hide'=>false, 'custom_filename' => false, 'parent_subroot_id'=>'root-master'),
            )
        ));
        parent::__construct($config);
    }
}
