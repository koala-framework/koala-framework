<?php
class Kwc_Basic_DownloadTagBehindLogin_Download extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['plugins']['password'] = 'Kwf_Component_Plugin_Password_Component';
        $ret['generators']['child']['component']['downloadTag'] = 'Kwc_Basic_DownloadTagBehindLogin_TestComponent';
        return $ret;
    }
}
