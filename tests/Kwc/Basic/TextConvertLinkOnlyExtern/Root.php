<?php
class Kwc_Basic_TextConvertLinkOnlyExtern_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1003, 'pos'=>4, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo3', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1004, 'pos'=>5, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo4', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1005, 'pos'=>6, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo5', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1006, 'pos'=>7, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo6', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1007, 'pos'=>8, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo7', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component'] = array('text' => 'Kwc_Basic_TextConvertLinkOnlyExtern_TestComponent');

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
