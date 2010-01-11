<?php
class Vpc_Basic_LinkTagIntern_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(

            //normaler, gültiger link
            array('id'=>1300, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo1',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false),

            //keine seite ausgewählt
            array('id'=>1301, 'pos'=>2, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo2',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false),

            //seite ausgewählt, aber nicht vorhanden
            array('id'=>1302, 'pos'=>3, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo3',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false),

            //seite ausgewählt, aber unsichtbar
            array('id'=>1303, 'pos'=>4, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo3',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'hide'=>false),


            //target für 1300
            array('id'=>1310, 'pos'=>5, 'visible'=>true, 'name'=>'Bar', 'filename' => 'bar',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),

            //target für 1303 (unsichtbar)
            array('id'=>1311, 'pos'=>6, 'visible'=>false, 'name'=>'Bar2', 'filename' => 'bar2',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false)
        )));
        $ret['generators']['page']['component'] = array(
            'link' => 'Vpc_Basic_LinkTagIntern_TestComponent',
            'empty' => 'Vpc_Basic_Empty_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
