<?php
class Kwf_Util_Facebook_Assets
{
    public static function getAppId()
    {
        if (isset(Kwf_Registry::get('config')->kwc->fbAppData->appId)) {
            return Kwf_Registry::get('config')->kwc->fbAppData->appId;
        }
        return '';
    }
}
