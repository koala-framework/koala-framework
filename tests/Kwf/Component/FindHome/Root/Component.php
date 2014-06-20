<?php
class Kwf_Component_FindHome_Root_Component extends Kwc_Root_DomainRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['domain']['component'] = 'Kwf_Component_FindHome_Root_Domain_Component';
        $ret['generators']['domain']['model'] = 'Kwf_Component_FindHome_Root_Model';
        return $ret;
    }
}
