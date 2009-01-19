<?php
class Vpc_Basic_Html_OfflineHidden_Component extends Vpc_Basic_Html_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Offline hidden Html');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if (!Vps_Registry::get('config')->isOnline) {
            $ret['content'] = trlVps('(offline hidden)');
        }
        return $ret;
    }
}
