<?php
class Kwf_Component_FindHome_Root_Domain_Component extends Kwc_Root_DomainRoot_Domain_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['category']['component'] = 'Kwf_Component_FindHome_Root_Domain_Category_Component';
        $ret['generators']['category']['model'] = 'Kwf_Component_FindHome_Root_Domain_Model';
        $ret['flags']['hasLanguage'] = true;
        return $ret;
    }
}
