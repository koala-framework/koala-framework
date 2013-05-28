<?php
class Kwf_Component_Generator_HomeNotTopLevel_Category extends Kwc_Root_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Level1', 'filename' => 'level1', 'custom_filename' => false,
                  'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Level2', 'filename' => 'level2', 'custom_filename' => false,
                  'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Level3', 'filename' => 'level3', 'custom_filename' => false,
                  'parent_id'=>2, 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root'),
        )));
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_None_Component'
        );
        return $ret;
    }
}
