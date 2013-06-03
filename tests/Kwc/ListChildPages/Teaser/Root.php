<?php
class Kwc_ListChildPages_Teaser_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>400, 'pos'=>1, 'visible'=>true, 'name'=>'name400', 'filename' => 'name400', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'listchild', 'is_home'=>true, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),

            array('id'=>500, 'pos'=>1, 'visible'=>true, 'name'=>'name500', 'filename' => 'name500', 'custom_filename' => false,
                  'parent_id'=>400, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>501, 'pos'=>2, 'visible'=>false, 'name'=>'name501', 'filename' => 'name501', 'custom_filename' => false,
                  'parent_id'=>400, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>502, 'pos'=>3, 'visible'=>true, 'name'=>'name502', 'filename' => 'name502', 'custom_filename' => false,
                  'parent_id'=>400, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component'] = array(
            'listchild' => 'Kwc_ListChildPages_Teaser_Teaser_Component',
            'empty' => 'Kwc_Basic_None_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
