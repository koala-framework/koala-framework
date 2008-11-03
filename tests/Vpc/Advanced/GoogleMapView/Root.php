<?php
class Vpc_Advanced_GoogleMapView_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>2000, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'gmapView', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>2001, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'gmapView', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>2002, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'gmapView', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
            array('id'=>2003, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>null, 'component'=>'gmapView', 'is_home'=>false, 'type'=>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'gmapView' => 'Vpc_Advanced_GoogleMapView_TestComponent',
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
