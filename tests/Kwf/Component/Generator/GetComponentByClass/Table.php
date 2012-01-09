<?php
class Kwf_Component_Generator_GetComponentByClass_Table extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_GetComponentByClass_Generator',
            'component' => 'Kwc_Basic_None_Component',
            'model' => new Kwf_Model_FnF(array(
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
