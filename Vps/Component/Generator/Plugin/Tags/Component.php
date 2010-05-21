<?php
class Vps_Component_Generator_Plugin_Tags_Component extends Vps_Component_Generator_Plugin_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Tags');
        $ret['assetsAdmin']['dep'][] = 'VpsAutoAssignGrid';
        return $ret;
    }
}
