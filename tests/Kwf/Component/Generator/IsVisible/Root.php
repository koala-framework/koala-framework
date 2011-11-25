<?php
class Kwf_Component_Generator_IsVisible_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwf_Component_Generator_IsVisible_Child',
            'model' => new Kwf_Model_FnF(array('data'=>array(
                array('id'=>1, 'visible'=>true),
                array('id'=>2, 'visible'=>false)
            )))
        );
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>false, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'child', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>1, 'component'=>'child', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'child', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'child' => 'Kwf_Component_Generator_IsVisible_Child'
        );
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>