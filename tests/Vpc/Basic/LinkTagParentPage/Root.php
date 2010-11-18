<?php
class Vpc_Basic_LinkTagParentPage_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1400, 'pos'=>1, 'visible'=>true, 'name'=>'Foo 1', 'filename' => 'foo1',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1401, 'pos'=>1, 'visible'=>true, 'name'=>'Foo 2', 'filename' => 'foo2',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1402, 'pos'=>1, 'visible'=>true, 'name'=>'Foo 1.1', 'filename' => 'foo11',
                  'parent_id'=>'1400', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'link' => 'Vpc_Basic_LinkTag_ParentPage_Component',
            'empty' => 'Vpc_Basic_Empty_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
