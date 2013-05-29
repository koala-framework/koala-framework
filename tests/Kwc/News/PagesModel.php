<?php
class Kwc_News_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>2100, 'pos'=>1, 'visible'=>true, 'name'=>'NewsBar', 'filename' => 'newsbar1', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'news', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            )
        ));
        parent::__construct($config);
    }
}
