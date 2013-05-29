<?php
class Kwc_Basic_Text_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1000, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>true, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1001, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo1', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1002, 'pos'=>3, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo2', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
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
            array('id'=>1008, 'pos'=>9, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo8', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1009, 'pos'=>10, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo9', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1010, 'pos'=>11, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo10', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1011, 'pos'=>12, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo11', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1012, 'pos'=>13, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo12', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1013, 'pos'=>14, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo13', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1014, 'pos'=>15, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo14', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1015, 'pos'=>16, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo15', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'text', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component'] = array('text' => 'Kwc_Basic_Text_TestComponent');

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
