<?php
class Kwf_Util_Hash
{
    public function hash($str)
    {
        $salt = Kwf_Cache_Simple::fetch('hashpp-');
        if (!$salt) {
            $hashFile = 'cache/hashprivatepart';
            if (!file_exists($hashFile)) {
                file_put_contents($hashFile, time().rand(100000, 1000000));
            }
            $salt = file_get_contents($hashFile);
            Kwf_Cache_Simple::add('hashpp-', $salt);
        }
        return md5($salt.$str);
    }
}
