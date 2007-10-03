<?php
class Vps_Media_Password
{
    const CACHE = 0;
    const ORIGINAL = 1;

    public static function get($type = self::CACHE)
    {
        switch ($type) {
            case self::CACHE:
                return 'l4Gx8SFe';
            case self::ORIGINAL:
                return 'k4Xjgw9f';
            default:
                return '';
        }
    }
}