<?php
class Vps_Component_Generator_DbId_Static extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['foo'] = array(
            'class' => 'Vps_Component_Generator_DbId_StaticGenerator',
            'component' => 'Vpc_Basic_Empty_Component',
            'dbIdShortcut' => 'test_',
            'nameColumn' => 'name',
            'model' => new Vps_Model_FnF(array(
            'columns' => array('id', 'name'),
            'data' => array(
                array('id'=>1, 'name'=>'Foo1'),
                array('id'=>2, 'name'=>'Foo2'),
                array('id'=>3, 'name'=>'Foo3'),
            )))
        );
        return $ret;
    }

}
