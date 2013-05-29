<?php
class Kwc_Basic_Image_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1600, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'imageFix', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1601, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'imageFix', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1602, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'imageFix', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1603, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'image', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1604, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'emptyImage', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1605, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'parentImage', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1606, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'bestFitWithZeroHeight', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1607, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1608, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1609, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1610, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1611, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1612, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1613, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1614, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1615, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1616, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'multipleDimensions', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),

        )));
        $ret['generators']['page']['component'] = array(
            'image' => 'Kwc_Basic_Image_TestComponent',
            'imageFix' => 'Kwc_Basic_Image_FixDimensionComponent',
            'emptyImage' => 'Kwc_Basic_Image_EmptyImageComponent',
            'parentImage' => 'Kwc_Basic_Image_ParentImageComponent_Component',
            'bestFitWithZeroHeight' => 'Kwc_Basic_Image_BestFitWithZeroHeightComponent',
            'multipleDimensions' => 'Kwc_Basic_Image_MultipleDimensionsComponent'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
