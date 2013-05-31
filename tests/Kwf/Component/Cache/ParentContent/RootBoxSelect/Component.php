<?php
class Kwf_Component_Cache_ParentContent_RootBoxSelect_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_StaticSelect',
            'component' => array(
                'parentContent' => 'Kwc_Basic_ParentContent_Component',
                'boxSelect' => 'Kwf_Component_Cache_ParentContent_RootBoxSelect_Box_Component',
            ),
            'inherit' => true,
            'model' => new Kwf_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data' => array(
                    array('component_id' => '1-box', 'component' => 'boxSelect'),
                    array('component_id' => '2-box', 'component' => 'parentContent'),
                )
            ))
        );
        $ret['generators']['page']['model'] = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'1', 'filename' => '1', 'custom_filename'=>false,
                'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'
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
