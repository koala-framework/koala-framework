<?php
class Kwc_Basic_LinkTagFirstChildPage_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            //erste-unterseite 1 ebene darunter
            array('id'=>1500, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo1', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1501, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'bar1',
                  'parent_id'=>1500, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),

            //erste-unterseite 2x hintereinander ebene darunter
            array('id'=>1502, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo2', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1503, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'bar2', 'custom_filename' => false,
                  'parent_id'=>1502, 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1504, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'baz2', 'custom_filename' => false,
                  'parent_id'=>1503, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),

            //keine unterseite
            array('id'=>1505, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo3', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component'] = array(
            'link' => 'Kwc_Basic_LinkTag_FirstChildPage_Component',
            'empty' => 'Kwc_Basic_None_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
