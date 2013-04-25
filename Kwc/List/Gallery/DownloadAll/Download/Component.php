<?php
class Kwc_List_Gallery_DownloadAll_Download_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Kwc_List_Gallery_DownloadAll_Download_ContentSender';
        return $ret;
    }
}
