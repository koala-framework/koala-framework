<?php
class Vpc_Trl_Columns_Columns_Column_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['ownModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id'
        ));
        $ret['componentName'] = 'Test Trl';
        return $ret;
    }
}
