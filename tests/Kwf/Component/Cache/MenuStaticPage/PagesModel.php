<?php
class Kwf_Component_Cache_MenuStaticPage_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'test1', 'filename' => 'test1', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
                array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'test2', 'filename' => 'test2', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            )
        ));
        parent::__construct($config);
    }
}
