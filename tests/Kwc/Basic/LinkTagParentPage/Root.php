<?php
class Kwc_Basic_LinkTagParentPage_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1400, 'pos'=>1, 'visible'=>true, 'name'=>'Foo 1', 'filename' => 'foo1', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1401, 'pos'=>1, 'visible'=>true, 'name'=>'Foo 2', 'filename' => 'foo2', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>1402, 'pos'=>1, 'visible'=>true, 'name'=>'Foo 1.1', 'filename' => 'foo11', 'custom_filename' => false,
                  'parent_id'=>'1400', 'component'=>'link', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component'] = array(
            'link' => 'Kwc_Basic_LinkTag_ParentPage_Component',
            'empty' => 'Kwc_Basic_None_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
