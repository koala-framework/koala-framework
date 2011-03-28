<?php
class Vpc_Basic_TextSessionModel_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1000, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'category' =>'main', 'hide'=>false),
            array('id'=>1001, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo1',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array('empty' => 'Vpc_Basic_Empty_Component');

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);

        $ret['generators']['text'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'text',
            'component' => 'Vpc_Basic_TextSessionModel_TestComponent'
        );
        return $ret;
    }
}