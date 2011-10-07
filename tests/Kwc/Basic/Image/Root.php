<?php
class Vpc_Basic_Image_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1600, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'imageFix', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1601, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'imageFix', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1602, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'imageFix', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1603, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'image', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1604, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'emptyImage', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1605, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'parentImage', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1606, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'bestFitWithZeroHeight', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1607, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1608, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1609, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1610, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1611, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1612, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1613, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1614, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1615, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1616, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false),

        )));
        $ret['generators']['page']['component'] = array(
            'image' => 'Vpc_Basic_Image_TestComponent',
            'imageFix' => 'Vpc_Basic_Image_FixDimensionComponent',
            'emptyImage' => 'Vpc_Basic_Image_EmptyImageComponent',
            'parentImage' => 'Vpc_Basic_Image_ParentImageComponent_Component',
            'bestFitWithZeroHeight' => 'Vpc_Basic_Image_BestFitWithZeroHeightComponent',
            'multipleDimensions' => 'Vpc_Basic_Image_MultipleDimensionsComponent'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
