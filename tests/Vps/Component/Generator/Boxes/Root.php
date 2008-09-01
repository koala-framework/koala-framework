<?php
class Vps_Component_Generator_Boxes_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>null, 'component'=>'empty', 'is_home'=>true, 'type'=>'main', 'hide'=>false)
        )));
        $ret['generators']['page']['component'] = array('empty' => 'Vpc_Basic_Empty_Component');

        $ret['generators']['box']['component'] = array(
            'box' => 'Vpc_Basic_Html_Component'
        );
        return $ret;
    }
}
