<?php
class Kwf_Component_Generator_AlternativeComponent_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'default', 'is_home'=>true, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                  'parent_id'=>1, 'component'=>'default', 'is_home'=>false, 'category' =>'main', 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component'] = array(
            'default' => 'Kwf_Component_Generator_AlternativeComponent_Component',
        );
        return $ret;
    }
}
