<?php
class Kwf_Component_Cache_MenuHasContent_Category_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'test1', 'filename' => 'test1', 'custom_filename' => false,
                    'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                    array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'test3', 'filename' => 'test3', 'custom_filename' => false,
                        'parent_id'=>'1', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                        array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'test4', 'filename' => 'test4', 'custom_filename' => false,
                            'parent_id'=>'3', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                array('id'=>2, 'pos'=>1, 'visible'=>false, 'name'=>'test2', 'filename' => 'test2', 'custom_filename' => false,
                    'parent_id'=>'root-top', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
            )
        ));
        parent::__construct($config);
    }
}
