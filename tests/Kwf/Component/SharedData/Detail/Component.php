<?php
class Kwf_Component_SharedData_Detail_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['shared'] = 'Kwf_Component_SharedData_Detail_SharedData_Component';
        return $ret;
    }
}
?>