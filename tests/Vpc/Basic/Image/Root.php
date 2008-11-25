<?php
class Vpc_Basic_Image_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1600, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'imageFix', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1601, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'imageFix', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1602, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'imageFix', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1603, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'image', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1604, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'emptyImage', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1605, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'parentImage', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1606, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'bestFitWithZeroHeight', 'is_home'=>false, 'category' =>'main', 'hide'=>false),

        )));
        $ret['generators']['page']['component'] = array(
            'image' => 'Vpc_Basic_Image_TestComponent',
            'imageFix' => 'Vpc_Basic_Image_FixDimensionComponent',
            'emptyImage' => 'Vpc_Basic_Image_EmptyImageComponent',
            'parentImage' => 'Vpc_Basic_Image_ParentImageComponent_Component',
            'bestFitWithZeroHeight' => 'tests_Vpc_Basic_Image_BestFitWithZeroHeightComponent'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
