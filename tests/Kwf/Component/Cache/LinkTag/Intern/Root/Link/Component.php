<?php
class Kwf_Component_Cache_LinkTag_Intern_Root_Link_Component extends Kwc_Basic_LinkTag_Intern_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwf_Component_Cache_LinkTag_Intern_Root_Link_Model';
        return $ret;
    }
}
