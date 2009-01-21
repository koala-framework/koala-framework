<?php
class Vps_Component_Generator_GetComponentByClass_Table extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_GetComponentByClass_Generator',
            'component' => 'Vpc_Basic_Empty_Component',
            'model' => new Vps_Model_FnF(array(
                'columns' => array('id', 'name'),
                'data'=>array(
                    array('id'=>1, 'name'=>'foo1'),
                    array('id'=>2, 'name'=>'foo2'),
                    array('id'=>3, 'name'=>'foo3')
                )))
        );
        return $ret;
    }

}
