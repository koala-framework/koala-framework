<?php
class Kwf_Util_BackendLoginRestriction
{
    public static function isAllowed()
    {
        if (Kwf_Config::getValue('blockExternalAdminAccess')) {
            $currentIp = $_SERVER['REMOTE_ADDR'];
            $valid = false;
            foreach (Kwf_Config::getValueArray('allowedAdminIPs') as $ip) {
                if (substr($ip, -1)=='*') {
                    $i = substr($ip, 0, -1);
                    if (substr($currentIp, 0, strlen($i)) == $i){
                        $valid = true;
                    }
                } else if (substr($ip, 0, 1)=='*') {
                    $i = substr($ip, 1);
                    if (substr($currentIp, -strlen($i)) == $i){
                        $valid = true;
                    }
                } else {
                    if ($currentIp == $ip){
                        $valid = true;
                    }
                }
            }
            if (!$valid) {
                throw new Kwf_Exception_AccessDenied();
            }
        }
    }
}
