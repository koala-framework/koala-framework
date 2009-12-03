<?php
class Vpc_Basic_LinkTagFirstChildPage_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            //erste-unterseite 1 ebene darunter
            array('id'=>1500, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo1',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1501, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'bar1',
                  'parent_id'=>1500, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),

            //erste-unterseite 2x hintereinander ebene darunter
            array('id'=>1502, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo2',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1503, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'bar2',
                  'parent_id'=>1502, 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1504, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'baz2',
                  'parent_id'=>1503, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),

            //keine unterseite
            array('id'=>1505, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo3',
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'link' => 'Vpc_Basic_LinkTag_FirstChildPage_Component',
            'empty' => 'Vpc_Basic_Empty_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
