<?php
class Vpc_Basic_Html_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>2000, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>'root', 'component'=>'html', 'is_home'=>true, 'category' =>'main', 'hide'=>false),
            array('id'=>2001, 'pos'=>2, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'html', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>2002, 'pos'=>3, 'visible'=>true, 'name'=>'Bar', 'filename' => 'bar',
                  'parent_id'=>'root', 'component'=>'html', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>2003, 'pos'=>4, 'visible'=>true, 'name'=>'Bar2', 'filename' => 'bar2',
                  'parent_id'=>'root', 'component'=>'html', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>2004, 'pos'=>5, 'visible'=>true, 'name'=>'Bar3', 'filename' => 'bar3',
                  'parent_id'=>'root', 'component'=>'html', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array('html' => 'Vpc_Basic_Html_TestComponent');

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
