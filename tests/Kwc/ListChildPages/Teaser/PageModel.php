<?php
class Kwc_ListChildPages_Teaser_PageModel extends Kwf_Model_FnF
{
    public function __construct($config = array())
    {
        $config['namespace'] = 'listchildpages_teaser_pagemodel';
        $config['primaryKey'] = 'id';
        $config['data'] = array(
            array('id'=>400, 'pos'=>1, 'visible'=>true, 'name'=>'name400', 'filename' => 'name400', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'listchild', 'is_home'=>true, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),

            array('id'=>500, 'pos'=>1, 'visible'=>true, 'name'=>'name500', 'filename' => 'name500', 'custom_filename' => false,
                  'parent_id'=>400, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>501, 'pos'=>2, 'visible'=>false, 'name'=>'name501', 'filename' => 'name501', 'custom_filename' => false,
                  'parent_id'=>400, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>502, 'pos'=>3, 'visible'=>true, 'name'=>'name502', 'filename' => 'name502', 'custom_filename' => false,
                  'parent_id'=>400, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),


            array('id'=>401, 'pos'=>1, 'visible'=>true, 'name'=>'name401', 'filename' => 'name401', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'listchildwithvisible', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),

            array('id'=>600, 'pos'=>1, 'visible'=>true, 'name'=>'name600', 'filename' => 'name600', 'custom_filename' => false,
                  'parent_id'=>401, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>601, 'pos'=>2, 'visible'=>false, 'name'=>'name601', 'filename' => 'name601', 'custom_filename' => false,
                  'parent_id'=>401, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>602, 'pos'=>3, 'visible'=>true, 'name'=>'name602', 'filename' => 'name602', 'custom_filename' => false,
                  'parent_id'=>401, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),

//             array('id'=>603, 'pos'=>4, 'visible'=>true, 'name'=>'name602', 'filename' => 'name602', 'custom_filename' => false,
//                   'parent_id'=>401, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
        );
        parent::__construct($config);
    }
}
