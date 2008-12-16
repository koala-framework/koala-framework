<?php
class Vps_Component_Cache_ClearMenu_Link extends Vpc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vps_Component_Cache_ClearMenu_LinkModel';
        $ret['generators']['link']['component']['intern'] = 'Vps_Component_Cache_ClearMenu_LinkIntern';
        return $ret;
    }
}
