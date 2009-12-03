<?php
class Vpc_Basic_ImageEnlarge_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1800, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo1',
                  'parent_id'=>'root', 'component'=>'imageWithoutSmall', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1801, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo2',
                  'parent_id'=>'root', 'component'=>'image', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1802, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo3',
                  'parent_id'=>'root', 'component'=>'image', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1803, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo4',
                  'parent_id'=>'root', 'component'=>'imageWithOriginal', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'image' => 'Vpc_Basic_ImageEnlarge_TestComponent',
            'imageWithoutSmall' => 'Vpc_Basic_ImageEnlarge_WithoutSmallImageComponent',
            'imageWithOriginal' => 'Vpc_Basic_ImageEnlarge_OriginalImageComponent',
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
