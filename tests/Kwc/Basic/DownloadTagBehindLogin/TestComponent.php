<?php
class Kwc_Basic_DownloadTagBehindLogin_TestComponent extends Kwc_Basic_DownloadTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_DownloadTagBehindLogin_TestModel';
        $ret['plugins']['password'] = 'Kwf_Component_Plugin_Password_Component';
        return $ret;
    }
}
