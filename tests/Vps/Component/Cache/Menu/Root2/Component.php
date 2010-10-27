<?php
class Vps_Component_Cache_Menu_Root2_Component extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'f1', 'filename' => 'f1',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'f2', 'filename' => 'f2',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'f3', 'filename' => 'f3',
                  'parent_id'=>'1', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'f4', 'filename' => 'f4',
                  'parent_id'=>'3', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
        )));
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component',
        );
        $ret['generators']['box']['component']['menu'] = 'Vps_Component_Cache_Menu_Root2_Menu_Component';
        $ret['flags']['menuCategory'] = 'root';
        return $ret;
    }
}
