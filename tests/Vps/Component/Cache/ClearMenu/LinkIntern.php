<?php
class Vps_Component_Cache_ClearMenu_LinkIntern extends Vpc_Basic_LinkTag_Intern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vps_Component_Cache_ClearMenu_LinkInternModel';
        return $ret;
    }
}
