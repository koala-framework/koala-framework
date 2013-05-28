<?php
class Kwf_Component_Fulltext_BasicHtml_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Test1', 'filename' => 'test1', 'custom_filename'=>false, 'parent_subroot_id'=>'root',
                    'parent_id'=>'root', 'component'=>'html', 'is_home'=>false, 'hide'=>false),

                array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Test2', 'filename' => 'test2', 'parent_subroot_id'=>'root', 'custom_filename' => false,
                        'parent_id'=>'root', 'component'=>'htmlChild', 'is_home'=>false, 'hide'=>false),

                array('id'=>3, 'pos'=>3, 'visible'=>true, 'name'=>'Test3', 'filename' => 'test3', 'parent_subroot_id'=>'root', 'custom_filename' => false,
                        'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            )
        ));
        parent::__construct($config);
    }
}
