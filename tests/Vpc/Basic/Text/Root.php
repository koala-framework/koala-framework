<?php
class Vpc_Basic_Text_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1000, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>true, 'type'=>'main', 'hide'=>false),
            array('id'=>1001, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1002, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1003, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1004, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1005, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1006, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1007, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1008, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1009, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1010, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1011, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1012, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1013, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'text', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array('text' => 'Vpc_Basic_Text_TestComponent');

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
