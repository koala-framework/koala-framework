<?php
class Vpc_Basic_DownloadTagBehindLogin_Download extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['plugins']['password'] = 'Vps_Component_Plugin_Password_Component';
        $ret['generators']['child']['component']['downloadTag'] = 'Vpc_Basic_DownloadTagBehindLogin_TestComponent';
        return $ret;
    }
}
