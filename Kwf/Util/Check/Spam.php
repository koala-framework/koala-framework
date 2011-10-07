<?php
class Vps_Util_Check_Spam
{
    private static $_backend;
    public static function setBackend(Vps_Util_Check_Spam_Backend_Interface $backend = null)
    {
        self::$_backend = $backend;
    }

    public static function checkIsSpam($text, $row = null)
    {
        if (!self::$_backend) {
            $c = Vps_Registry::get('config')->spamChecker;
            if (!$c) return false;
            self::$_backend = new $c;
        }
        return self::$_backend->checkIsSpam($text, $row);
    }

    static public function getSpamKey($row)
    {
        return substr(md5(serialize($row->id.$row->save_date)), 0, 15);
    }


}
