<?php
class Kwc_Basic_TextSessionModel_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1000, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1001, 'pos'=>2, 'visible'=>true, 'name'=>'foo', 'filename' => 'foo1', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component'] = array('empty' => 'Kwc_Basic_None_Component');

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);

        $ret['generators']['text'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'text',
            'component' => 'Kwc_Basic_TextSessionModel_TestComponent'
        );
        return $ret;
    }
}
