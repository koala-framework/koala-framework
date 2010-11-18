<?php
class Vpc_Columns_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>3000, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'columns', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'columns' => 'Vpc_Columns_TestComponent',
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
