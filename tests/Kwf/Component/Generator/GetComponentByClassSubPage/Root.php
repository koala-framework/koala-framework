<?php
class Kwf_Component_Generator_GetComponentByClassSubPage_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'category' =>'main', 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home', 'custom_filename' => false,
                  'parent_id'=>1, 'component'=>'empty', 'is_home'=>true, 'category' =>'main', 'hide'=>false)
            )));
        $ret['generators']['page']['component'] = array('empty' => 'Kwc_Basic_None_Component');
        return $ret;
    }
}
