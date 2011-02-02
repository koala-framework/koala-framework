<?php
class Vps_Component_Cache_Box_IcRoot_Component extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['ic'] = array(
            'component' => 'Vps_Component_Cache_Box_IcRoot_InheritContent_Component',
            'class' => 'Vps_Component_Generator_Box_Static',
            'inherit' => true
        );

        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'1', 'filename' => '1',
                'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false
            ),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'2', 'filename' => '2',
                'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'hide'=>false
            )

        )));
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component',
        );
        return $ret;
    }
}
