<?php
class Vps_Component_Generator_HomeNotTopLevel_Category extends Vpc_Root_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Level1', 'filename' => 'level1',
                  'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Level2', 'filename' => 'level2',
                  'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Level3', 'filename' => 'level3',
                  'parent_id'=>2, 'component'=>'empty', 'is_home'=>true, 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component'
        );
        return $ret;
    }
}
