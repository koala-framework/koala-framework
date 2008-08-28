<?php
class Vps_Component_Generator_Inherit_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>null, 'component'=>'empty', 'is_home'=>true, 'type'=>'main')
        )));
        $ret['generators']['page']['component'] = array('empty' => 'Vpc_Basic_Empty_Component');

        $ret['generators']['box']['component'] = array(
            'box' => 'Vps_Component_Generator_Inherit_Box'
        );
        
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'name' => 'Static'
        );
        return $ret;
    }
}
