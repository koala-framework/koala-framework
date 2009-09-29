<?php
class Vps_Component_CacheVars_Link_Intern extends Vpc_Basic_LinkTag_Intern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vps_Component_CacheVars_Link_InternModel';
        return $ret;
    }
}
