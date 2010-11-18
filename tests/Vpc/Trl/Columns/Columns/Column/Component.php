<?php
class Vpc_Trl_Columns_Columns_Column_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id'
        ));
        $ret['componentName'] = 'Test';
        return $ret;
    }
}
