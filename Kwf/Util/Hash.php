<?php
class Kwf_Util_Hash
{
    public static function hash($str)
    {
        $salt = Kwf_Cache_SimpleStatic::fetch('hashpp-');
        if (!$salt) {
            if ($salt = Kwf_Config::getSetting('hashprivatepart')) {
                //defined in config, required if multiple webservers should share the same salt
            } else {
                $hashFile = 'cache/hashprivatepart';
                if (!file_exists($hashFile)) {
                    file_put_contents($hashFile, time().rand(100000, 1000000));
                }
                $salt = file_get_contents($hashFile);
                Kwf_Cache_SimpleStatic::add('hashpp-', $salt);
            }
        }
        return md5($salt.$str);
    }
}
