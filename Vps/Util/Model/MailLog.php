<?php
class Vps_Util_Model_MailLog extends Vps_Model_Service
{
    protected function _init()
    {
        $maillogUrl = Vps_Registry::get('config')->service->maillog->url;
        $this->_client = new Vps_Srpc_Client(array('serverUrl' => $maillogUrl));
    }

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
