<?php
class Kwc_Basic_LinkTagNews_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(

                // necessary news component
                array('id'=>2100, 'pos'=>1, 'visible'=>true, 'name'=>'NewsBar', 'filename' => 'newsbar1', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'news', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

                // real link
                array('id'=>5100, 'pos'=>2, 'visible'=>true, 'name'=>'NewsLink 1', 'filename' => 'newslink1', 'custom_filename' => false,
                        'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

                // dummy
                array('id'=>5200, 'pos'=>3, 'visible'=>true, 'name'=>'NewsLink 2', 'filename' => 'newslink2', 'custom_filename' => false,
                        'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

            )
        ));
        parent::__construct($config);
    }
}
