<?php
class Vps_Util_Check_Spam
{
    public static function checkIsSpam($text, $row = null)
    {
        $c = Vps_Registry::get('config')->spamChecker;
        if (!$c) return false;
        $c = new $c;
        return $c->checkIsSpam($text, $row);
    }

    static public function getSpamKey($row)
    {
        return substr(md5(serialize($row->id.$row->save_date)), 0, 15);
    }


}
