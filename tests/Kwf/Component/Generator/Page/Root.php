<?php
class Kwf_Component_Generator_Page_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'child', 'is_home'=>true, 'category' =>'main', 'hide'=>false, 'parent_subroot_id'=>'root'),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id'=>'root'),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Foo2', 'filename' => 'foo2', 'custom_filename' => false,
                  'parent_id'=>2, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id'=>'root'),
            array('id'=>4, 'pos'=>2, 'visible'=>true, 'name'=>'Foo3', 'filename' => 'foo3', 'custom_filename' => false,
                  'parent_id'=>2, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id'=>'root'),
        )));
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_None_Component',
            'child' => 'Kwf_Component_Generator_Page_Child'
        );
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>
