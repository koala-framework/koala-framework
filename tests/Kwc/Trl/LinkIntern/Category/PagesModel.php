<?php
class Kwc_Trl_LinkIntern_Category_PagesModel extends Kwf_Model_FnF
{
    public function __construct()
    {
        $config['data'] = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo1', 'custom_filename' => false,
                'parent_id'=>'root-master-cat1', 'component'=>'none', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-master'),

            array('id'=>2, 'pos'=>2, 'visible'=>false, 'name'=>'Foo', 'filename' => 'foo2', 'custom_filename' => false,
                'parent_id'=>'root-master-cat1', 'component'=>'none', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-master'),

            array('id'=>3, 'pos'=>3, 'visible'=>false, 'name'=>'Foo', 'filename' => 'foo3', 'custom_filename' => false,
                'parent_id'=>'root-master-cat1', 'component'=>'none', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-master'),

        );
        parent::__construct($config);
    }
}
