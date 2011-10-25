<?php
class Kwf_Util_Model_MailLog extends Kwf_Model_Service
{
    protected $_serverConfig = 'maillog';

    public static function isAvailable()
    {
        if (Kwf_Registry::get('config')->service->maillog
            && Kwf_Registry::get('config')->service->maillog->url
        ) {
            return true;
        }
        return false;
    }
}
