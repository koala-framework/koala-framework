<?php
class Vps_Component_CacheVars_Link_Link extends Vpc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vps_Component_CacheVars_Link_Model';
        $ret['generators']['link']['component']['intern'] =
            'Vps_Component_CacheVars_Link_Intern';
        return $ret;
    }
}
