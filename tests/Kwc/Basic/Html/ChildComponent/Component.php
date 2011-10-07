<?php
class Vpc_Basic_Html_ChildComponent_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = new Vps_Model_FnF(array('primaryKey' => 'component_id'));
        return $ret;
    }

}
