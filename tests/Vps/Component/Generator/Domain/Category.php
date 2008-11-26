<?php
class Vps_Component_Generator_Domain_Category extends Vpc_Root_DomainRoot_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>null, 'component'=>'empty', 'is_home'=>true, 'category'=>'main', 'domain' => 'at', 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'category'=>'main', 'domain' => 'at', 'hide'=>false),
            array('id'=>4, 'pos'=>2, 'visible'=>true, 'name'=>'Foo3', 'filename' => 'foo3',
                  'parent_id'=>null, 'component'=>'empty', 'is_home'=>false, 'category'=>'bottom', 'domain' => 'at', 'hide'=>false),
            array('id'=>5, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>null, 'component'=>'empty', 'is_home'=>true, 'category'=>'main', 'domain' => 'ch', 'hide'=>false),
            array('id'=>6, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>5, 'component'=>'empty', 'is_home'=>false, 'category'=>'main', 'domain' => 'ch', 'hide'=>false),
            array('id'=>7, 'pos'=>2, 'visible'=>true, 'name'=>'Foo3', 'filename' => 'foo3',
                  'parent_id'=>null, 'component'=>'empty', 'is_home'=>false, 'category'=>'bottom', 'domain' => 'ch', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component'
        );
        return $ret;
    }
}
?>