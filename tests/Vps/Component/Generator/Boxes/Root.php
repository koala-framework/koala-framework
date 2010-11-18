<?php
class Vps_Component_Generator_Boxes_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'category' =>'main', 'hide'=>false)
        )));
        $ret['generators']['page']['component'] = array('empty' => 'Vpc_Basic_Empty_Component');

        unset($ret['generators']['title']);
        $ret['generators']['box']['component'] = array(
            'box' => 'Vpc_Basic_Html_Component'
        );
        return $ret;
    }
}
