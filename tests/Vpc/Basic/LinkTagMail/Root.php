<?php
class Vpc_Basic_LinkTagMail_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1400, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'link', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1401, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'link', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>1402, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'link', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'link' => 'Vpc_Basic_LinkTagMail_TestComponent'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
