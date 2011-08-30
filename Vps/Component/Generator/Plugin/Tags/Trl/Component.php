<?php
class Vps_Component_Generator_Plugin_Tags_Trl_Component extends Vps_Component_Generator_Plugin_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['componentName'] = trlVps('Tags Translation');
        $ret['componentIcon'] = new Vps_Asset('tag_blue.png');
        $ret['flags']['hasResources'] = true;
        return $ret;
    }
}
