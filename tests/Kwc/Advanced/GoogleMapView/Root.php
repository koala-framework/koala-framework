<?php
class Kwc_Advanced_GoogleMapView_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>2000, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'gmapView', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>2001, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'gmapView', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>2002, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'gmapView', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>2003, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'gmapView', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'gmapView' => 'Kwc_Advanced_GoogleMapView_TestComponent',
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
