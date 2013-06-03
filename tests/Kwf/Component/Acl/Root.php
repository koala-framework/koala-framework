<?php
class Kwf_Component_Acl_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Test2', 'filename' => 'test2', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'empty2', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>3, 'pos'=>3, 'visible'=>true, 'name'=>'Test3', 'filename' => 'test3', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'testComponent', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'Test4', 'filename' => 'test4', 'custom_filename' => false,
                  'parent_id'=>3, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component'] = array('empty' => 'Kwc_Basic_None_Component',
                                                        'empty2' => 'Kwf_Component_Acl_Empty2',
                                                        'testComponent' => 'Kwf_Component_Acl_TestComponent');
        return $ret;
    }
}
