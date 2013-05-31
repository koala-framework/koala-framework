<?php
class Kwf_Component_Cache_ParentContent_RootCategoriesBoxSelect_Category_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'home', 'filename' => 'home', 'custom_filename'=>false,
                    'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root'),
                    array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'foo1', 'filename' => 'foo1', 'custom_filename'=>false,
                        'parent_id'=>'1', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
                array('id'=>3, 'pos'=>3, 'visible'=>true, 'name'=>'foo2', 'filename' => 'foo2', 'custom_filename'=>false,
                    'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            )
        ));
        parent::__construct($config);
    }
}
