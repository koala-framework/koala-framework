<?php
class Kwc_Trl_Columns_Columns_Column_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id'
        ));
        $ret['componentName'] = 'Test';
        return $ret;
    }
}
