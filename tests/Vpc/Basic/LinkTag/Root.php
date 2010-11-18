<?php
class Vpc_Basic_LinkTag_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>'1100', 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>'1101', 'pos'=>2, 'visible'=>true, 'name'=>'Foo2', 'filename' => 'foo2',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false)
        )));
        $ret['generators']['page']['component'] = array('link' => 'Vpc_Basic_LinkTag_TestComponent');

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
