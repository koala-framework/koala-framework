<?php
class Vpc_Basic_DownloadTagBehindLogin_TestComponent extends Vpc_Basic_DownloadTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_DownloadTagBehindLogin_TestModel';
        $ret['plugins']['password'] = 'Vps_Component_Plugin_Password_Component';
        return $ret;
    }
}
