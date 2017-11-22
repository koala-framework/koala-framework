<?php
class Kwf_Util_Hash
{
    public static function getPrivatePart()
    {
        $salt = Kwf_Cache_SimpleStatic::fetch('hashpp-');
        if (!$salt) {
            if ($salt = Kwf_Config::getValue('hashPrivatePart')) {
                //defined in config, required if multiple webservers should share the same salt
            } else {
                $hashFile = 'cache/hashprivatepart';
                if (!file_exists($hashFile)) {
                    if (!function_exists('random_bytes')) {
                        require 'vendor/paragonie/random_compat/lib/random.php';
                    }
                    file_put_contents($hashFile, bin2hex(random_bytes(32)));
                }
                $salt = file_get_contents($hashFile);
                Kwf_Cache_SimpleStatic::add('hashpp-', $salt);
            }
        }
        return $salt;
    }

    public static function hash($str)
    {
        return md5(self::getPrivatePart().$str);
    }
}
