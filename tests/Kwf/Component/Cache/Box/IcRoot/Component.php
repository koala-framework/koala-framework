<?php
class Kwf_Component_Cache_Box_IcRoot_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['ic'] = array(
            'component' => 'Kwf_Component_Cache_Box_IcRoot_InheritContent_Component',
            'class' => 'Kwf_Component_Generator_Box_Static',
            'inherit' => true
        );

        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'1', 'filename' => '1', 'custom_filename'=>false,
                'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root',
            ),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'2', 'filename' => '2', 'custom_filename'=>false,
                'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'
            )

        )));
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        return $ret;
    }
}
