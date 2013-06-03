<?php
class Kwf_Component_ExpandedComponentId_Category extends Kwc_Root_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home', 'parent_subroot_id'=>'root', 'custom_filename'=>false,
                  'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'parent_subroot_id'=>'root', 'custom_filename'=>false,
                  'parent_id'=>1, 'component'=>'child', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component']['empty'] = 'Kwc_Basic_Empty_Component';
        $ret['generators']['page']['component']['child'] = 'Kwf_Component_ExpandedComponentId_Child';
        return $ret;
    }
}
?>
