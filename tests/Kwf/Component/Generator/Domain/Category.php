<?php
class Vps_Component_Generator_Domain_Category extends Vpc_Root_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>'root-at-main', 'component'=>'empty', 'is_home'=>true, 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>1, 'component'=>'empty_ch', 'is_home'=>false, 'hide'=>false),
            array('id'=>4, 'pos'=>2, 'visible'=>true, 'name'=>'Foo3', 'filename' => 'foo3',
                  'parent_id'=>'root-at-bottom', 'component'=>'image', 'is_home'=>false, 'hide'=>false),
            array('id'=>5, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>'root-ch-main', 'component'=>'image', 'is_home'=>true, 'hide'=>false),
            array('id'=>6, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>5, 'component'=>'empty_ch', 'is_home'=>false, 'hide'=>false),
            array('id'=>7, 'pos'=>2, 'visible'=>true, 'name'=>'Foo3', 'filename' => 'foo3',
                  'parent_id'=>'root-ch-bottom', 'component'=>'empty_ch', 'is_home'=>false, 'hide'=>false),
            )));
        $ret['generators']['page']['component'] = array(
            'image' => 'Vpc_Basic_Image_Component',
            'empty' => 'Vpc_Basic_Empty_Component',
            'empty_ch' => 'Vpc_Basic_Link_Component',
        );
        return $ret;
    }
}
?>