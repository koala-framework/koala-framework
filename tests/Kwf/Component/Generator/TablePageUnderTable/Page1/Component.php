<?php
class Vps_Component_Generator_TablePageUnderTable_Page1_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['table'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'model' => new Vps_Model_FnF(array(
                'data' => array(
                    array('id'=>1),
                    array('id'=>2),
                    array('id'=>3),
                    array('id'=>4),
                )
            )),
            'component' => 'Vps_Component_Generator_TablePageUnderTable_Page1_Child_Component'
        );
        return $ret;
    }
}
