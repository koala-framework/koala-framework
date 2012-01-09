<?php
class Kwf_Component_Generator_DbId_Static extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['foo'] = array(
            'class' => 'Kwf_Component_Generator_DbId_StaticGenerator',
            'component' => 'Kwc_Basic_None_Component',
            'dbIdShortcut' => 'test_',
            'nameColumn' => 'name',
            'model' => new Kwf_Model_FnF(array(
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
