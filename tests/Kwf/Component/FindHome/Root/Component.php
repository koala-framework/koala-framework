<?php
class Kwf_Component_FindHome_Root_Component extends Kwc_Root_DomainRoot_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['domain']['component'] = 'Kwf_Component_FindHome_Root_Domain_Component';
        $ret['generators']['domain']['model'] = 'Kwf_Component_FindHome_Root_Model';
        return $ret;
    }
}
