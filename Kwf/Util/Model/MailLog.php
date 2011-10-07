<?php
class Vps_Util_Model_MailLog extends Vps_Model_Service
{
    protected $_serverConfig = 'maillog';

    public static function isAvailable()
    {
        if (Vps_Registry::get('config')->service->maillog
            && Vps_Registry::get('config')->service->maillog->url
        ) {
            return true;
        }
        return false;
    }
}
