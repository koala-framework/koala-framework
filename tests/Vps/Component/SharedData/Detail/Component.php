<?php
class Vps_Component_SharedData_Detail_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['shared'] = 'Vps_Component_SharedData_Detail_SharedData_Component';
        return $ret;
    }
}
?>