<?php
class Kwf_Component_Cache_MenuHasContent_Category_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'test1', 'filename' => 'test1', 'custom_filename' => false,
                    'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
                array('id'=>2, 'pos'=>1, 'visible'=>false, 'name'=>'test2', 'filename' => 'test2', 'custom_filename' => false,
                    'parent_id'=>'root-top', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            )
        ));
        parent::__construct($config);
    }
}
