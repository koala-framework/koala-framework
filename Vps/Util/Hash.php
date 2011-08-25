<?php
class Vps_Util_Hash
{
    public function hash($str)
    {
        $hashFile = 'application/cache/hashprivatepart';
        if (!file_exists($hashFile)) {
            file_put_contents($hashFile, time().rand(100000, 1000000));
        }
        return md5(file_get_contents($hashFile).$str);
    }
}
