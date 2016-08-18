<?php
class Kwc_Trl_Legacy_Columns_Columns_Column_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponent = null)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['ownModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id'
        ));
        $ret['componentName'] = 'Test Trl';
        return $ret;
    }
}
