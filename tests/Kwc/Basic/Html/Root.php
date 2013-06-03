<?php
class Kwc_Basic_Html_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>2000, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'html', 'is_home'=>true, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>2001, 'pos'=>2, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'html', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>2002, 'pos'=>3, 'visible'=>true, 'name'=>'Bar', 'filename' => 'bar', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'html', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>2003, 'pos'=>4, 'visible'=>true, 'name'=>'Bar2', 'filename' => 'bar2', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'html', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>2004, 'pos'=>5, 'visible'=>true, 'name'=>'Bar3', 'filename' => 'bar3', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'html', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component'] = array('html' => 'Kwc_Basic_Html_TestComponent');

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
