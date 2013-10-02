<?php
class Kwc_Basic_LinkIntern_PagesModel extends Kwf_Model_FnF
{
    public function __construct($config = array())
    {
        $config['data'] =array(
            array('id'=>'1', 'pos'=>1, 'visible'=>true, 'name'=>'Link', 'filename' => 'link', 'custom_filename' => false,
                            'parent_id'=>'root', 'component'=>'linktagintern', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>'2', 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                            'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>'3', 'pos'=>2, 'visible'=>true, 'name'=>'Bar', 'filename' => 'bar', 'custom_filename' => false,
                            'parent_id'=>'2', 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
        );
        parent::__construct($config);
    }
}
