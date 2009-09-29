<?php
class Vps_Component_Generator_Count_Directory extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = new Vps_Model_FnF(array(
            'columns' => array('id', 'name'),
            'data' => array(
                array('id'=>1, 'name'=>'Foo'),
                array('id'=>2, 'name'=>'Bar'),
                array('id'=>3, 'name'=>'Baz'),
                array('id'=>4, 'name'=>'Foo1'),
                array('id'=>5, 'name'=>'Foo2'),
                array('id'=>6, 'name'=>'Foo3'),
                array('id'=>7, 'name'=>'Foo4'),
                array('id'=>8, 'name'=>'Foo5'),
            )
        ));
        $ret['generators']['detail']['nameColumn'] = 'name';
        return $ret;
    }
}
