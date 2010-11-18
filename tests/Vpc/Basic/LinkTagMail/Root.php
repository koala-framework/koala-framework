<?php
class Vpc_Basic_LinkTagMail_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1400, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1401, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1402, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'link' => 'Vpc_Basic_LinkTagMail_TestComponent'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
