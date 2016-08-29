<?php
class Kwc_Basic_Html_ChildComponent_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = new Kwf_Model_FnF(array('primaryKey' => 'component_id'));
        return $ret;
    }

}
