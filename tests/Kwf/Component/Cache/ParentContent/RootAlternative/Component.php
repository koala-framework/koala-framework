<?php
class Kwf_Component_Cache_ParentContent_RootAlternative_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        
        $ret['generators']['box']['component']['box'] = 'Kwf_Component_Cache_ParentContent_RootAlternative_Box_Component';
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'1', 'filename' => '1', 'custom_filename'=>false, 'parent_subroot_id' => 'root',
                'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false
            ),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'2', 'filename' => '2', 'custom_filename'=>false, 'parent_subroot_id' => 'root',
                'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'hide'=>false
            )

        )));
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        return $ret;
    }
}
