<?php
class Kwc_Trl_Domains_Root extends Kwc_Root_DomainRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['generators']['domain']['component'] = 'Kwc_Trl_Domains_Domain_Component';
        $ret['generators']['domain']['model'] = 'Kwc_Trl_Domains_DomainsModel';
        return $ret;
    }
}
