<?php
class Kwc_Cc_CopyPages_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Foo1', 'filename' => 'foo1', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'paragraphs', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Foo2', 'filename' => 'foo2', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'copyPages', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
            'paragraphs' => 'Kwc_Cc_CopyPages_Paragraphs_Component',
            'copyPages' => 'Kwc_Cc_CopyPages_CopyPages_Component',
        );
        return $ret;
    }
}
